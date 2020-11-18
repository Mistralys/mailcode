<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode statement tokenizer: parses a mailcode statement
 * into its logical parts.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer
{
    const ERROR_TOKENIZE_METHOD_MISSING = 49801;
    const ERROR_INVALID_TOKEN_CREATED = 49802;
    
    /**
     * @var string[]
     */
    protected $tokenCategories = array(
        'Variables',
        'NormalizeQuotes',
        'EscapedQuotes',
        'StringLiterals',
        'Keywords',
        'Numbers',
        'Operands',
        'ExtractTokens'
    );
    
   /**
    * @var Mailcode_Parser_Statement
    */
    protected $statement;
    
   /**
    * @var string
    */
    protected $tokenized;
    
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    protected $tokensOrdered = array();
    
   /**
    * @var string[]
    */
    protected static $ids = array();

    /**
     * @var callable[]
     */
    protected $changeHandlers = array();

    public function __construct(Mailcode_Parser_Statement $statement)
    {
        $this->statement = $statement;

        $this->tokenize($statement->getStatementString());
    }

   /**
    * Retrieves all tokens detected in the statement string, in 
    * the order they were found.
    * 
    * @return Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    public function getTokens()
    {
        return $this->tokensOrdered;
    }

    public function hasTokens() : bool
    {
        return !empty($this->tokensOrdered);
    }
    
   /**
    * Whether there were any unknown tokens in the statement.
    * 
    * @return bool
    */
    public function hasUnknown() : bool
    {
        $unknown = $this->getUnknown();
        
        return !empty($unknown);
    }
    
   /**
    * Retrieves all unknown content tokens, if any.
    * 
    * @return Mailcode_Parser_Statement_Tokenizer_Token_Unknown[]
    */
    public function getUnknown()
    {
        $result = array();
        
        foreach($this->tokensOrdered as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Unknown)
            {
                $result[] = $token;
            }
        }
        
        return $result;
    }
    
    public function getFirstUnknown() : ?Mailcode_Parser_Statement_Tokenizer_Token_Unknown
    {
        $unknown = $this->getUnknown();
        
        if(!empty($unknown))
        {
            return array_shift($unknown);
        }
        
        return null;
    }
    
    public function getNormalized() : string
    {
        $parts = array();
        
        foreach($this->tokensOrdered as $token)
        {
            $string = $token->getNormalized();
            
            if($string != '')
            {
                $parts[] = $string;
            }
        }
        
        return implode(' ', $parts);
    }

    /**
     * Goes through all tokenization processors, in the order that
     * they are defined in the tokenCategories property. This filters
     * the statement string, and extracts the tokens contained within.
     *
     * @param string $statement
     * @throws Mailcode_Exception
     *
     * @see Mailcode_Parser_Statement_Tokenizer_Process
     */
    protected function tokenize(string $statement) : void
    {
        $statement = trim($statement);
        $tokens = array();

        foreach($this->tokenCategories as $tokenCategory)
        {
            $processor = $this->createProcessor($tokenCategory, $statement, $tokens);
            $processor->process();

            $statement = $processor->getStatement();
            $tokens = $processor->getTokens();
        }

        $this->tokenized = $statement;
        $this->tokensOrdered = $tokens;
    }

    /**
     * @param string $id
     * @param string $statement
     * @param Mailcode_Parser_Statement_Tokenizer_Token[] $tokens
     * @return Mailcode_Parser_Statement_Tokenizer_Process
     * @throws Mailcode_Exception
     */
    protected function createProcessor(string $id, string $statement, array $tokens) : Mailcode_Parser_Statement_Tokenizer_Process
    {
        $class = 'Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_'.$id;

        $instance = new $class($this, $statement, $tokens);

        if($instance instanceof Mailcode_Parser_Statement_Tokenizer_Process)
        {
            return $instance;
        }

        throw new Mailcode_Exception(
            'Unknown statement token.',
            sprintf(
                'The tokenize class [%s] is not present.',
                $class
            ),
            self::ERROR_TOKENIZE_METHOD_MISSING
        );
    }

    /**
     * @param string $type
     * @param string $matchedText
     * @param mixed $subject
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    public function createToken(string $type, string $matchedText, $subject=null) : Mailcode_Parser_Statement_Tokenizer_Token
    {
        $tokenID = $this->generateID();

        $class = '\Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_'.$type;

        return new $class($tokenID, $matchedText, $subject);
    }

    public function appendKeyword(string $name) : Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        $name = rtrim($name, ':').':';

        $token = $this->appendToken('Keyword', $name);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $token;
        }

        throw new Mailcode_Exception(
            'Invalid token created',
            '',
            self::ERROR_INVALID_TOKEN_CREATED
        );
    }

    public function removeToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : Mailcode_Parser_Statement_Tokenizer
    {
        $keep = array();
        $tokenID = $token->getID();

        foreach ($this->tokensOrdered as $checkToken)
        {
            if($checkToken->getID() !== $tokenID)
            {
                $keep[] = $checkToken;
            }
        }

        $this->tokensOrdered = $keep;

        $this->triggerTokensChanged();

        return $this;
    }

    /**
     * @param string $type
     * @param string $matchedText
     * @param mixed $subject
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    protected function appendToken(string $type, string $matchedText, $subject=null) : Mailcode_Parser_Statement_Tokenizer_Token
    {
        $token = $this->createToken($type, $matchedText, $subject);

        $this->tokensOrdered[] = $token;

        $this->triggerTokensChanged();

        return $token;
    }
    
   /**
    * Generates a unique alphabet-based ID without numbers
    * to use as token name, to avoid conflicts with the
    * numbers detection.
    *
    * @return string
    */
    protected function generateID() : string
    {
        static $alphas;

        if(!isset($alphas))
        {
            $alphas = range('A', 'Z');
        }

        $amount = 12;

        $result = '';

        for($i=0; $i < $amount; $i++)
        {
            $result .= $alphas[array_rand($alphas)];
        }

        if(!in_array($result, self::$ids))
        {
            self::$ids[] = $result;
            return $result;
        }

        return $this->generateID();
    }

    /**
     * @param callable $callback
     */
    public function onTokensChanged($callback) : void
    {
        if(is_callable($callback))
        {
            $this->changeHandlers[] = $callback;
        }
    }

    protected function triggerTokensChanged() : void
    {
        foreach ($this->changeHandlers as $callback)
        {
            $callback($this);
        }
    }
}
