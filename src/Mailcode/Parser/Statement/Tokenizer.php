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
    
   /**
    * @var string[]
    */
    protected $operands = array(
        '==',
        '<=',
        '>=',
        '!=',
        '=',
        '+',
        '-',
        '/',
        '*',
        '>',
        '<'
    );
    
   /**
    * @var string[]
    */
    protected $keywords = array(
        'in:',
        'insensitive:',
        'urlencode:'
    );
    
   /**
    * @var string
    */
    protected $delimiter = '§§';
    
    /**
     * @var string[]
     */
    protected $tokenCategories = array(
        'variables',
        'normalize_quotes',
        'escaped_quotes',
        'string_literals',
        'keywords',
        'numbers',
        'operands',
        'extract_tokens'
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
    protected $tokensTemporary = array();
    
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    protected $tokensOrdered = array();
    
   /**
    * @var string[]
    */
    protected static $ids = array();
    
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
    * @return \Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Unknown[]
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
    
    protected function tokenize(string $statement) : void
    {
        $this->tokenized = trim($statement);
        
        foreach($this->tokenCategories as $token)
        {
            $method = 'tokenize_'.$token;
            
            if(!method_exists($this, $method))
            {
                throw new Mailcode_Exception(
                    'Unknown statement token.',
                    sprintf(
                        'The tokenize method [%s] is not present in class [%s].',
                        $method,
                        get_class($this)
                    ),
                    self::ERROR_TOKENIZE_METHOD_MISSING
                );
            }
            
            $this->$method();
        }
    }
   
   /**
    * Registers a token to add in the statement string.
    * 
    * @param string $type
    * @param string $matchedText
    * @param mixed $subject
    */
    protected function registerToken(string $type, string $matchedText, $subject=null) : void
    {
        $tokenID = $this->generateID();
        
        $this->tokenized = str_replace(
            $matchedText,
            $this->delimiter.$tokenID.$this->delimiter,
            $this->tokenized
        );
        
        $class = '\Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_'.$type;
        
        $this->tokensTemporary[] = new $class($tokenID, $matchedText, $subject);
    }
    
    protected function getTokenByID(string $tokenID) : ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        foreach($this->tokensTemporary as $token)
        {
            if($token->getID() === $tokenID)
            {
                return $token;
            }
        }
        
        return null;
    }
    
   /**
    * Some WYSIWYG editors like using pretty quotes instead
    * of the usual double quotes. This simply replaces all
    * occurrences with the regular variant.
    */
    protected function tokenize_normalize_quotes() : void
    {
        $this->tokenized = str_replace(array('“', '”'), '"', $this->tokenized);
    }
    
    protected function tokenize_escaped_quotes() : void
    {
        $this->tokenized = str_replace('\"', '__QUOTE__', $this->tokenized);
    }
    
    protected function tokenize_keywords() : void
    {
        foreach($this->keywords as $keyword)
        {
            if(strstr($this->tokenized, $keyword))
            {
                $this->registerToken('Keyword', $keyword);
            }
        }
    }
    
    protected function tokenize_extract_tokens() : void
    {
        // split the string by the delimiters: this gives an
        // array with tokenIDs, and any content that may be left
        // over that could not be tokenized.
        $parts = \AppUtils\ConvertHelper::explodeTrim($this->delimiter, $this->tokenized);

        foreach($parts as $part)
        {
            $token = $this->getTokenByID($part);
            
            // if the entry is a token, simply add it.
            if($token)
            {
                $this->tokensOrdered[] = $token;
            }
            // anything else is added as an unknown token.
            else 
            {
                $this->tokensOrdered[] = new Mailcode_Parser_Statement_Tokenizer_Token_Unknown($this->generateID(), $part);
            }
        }
    }
        
    protected function tokenize_variables() : void
    {
        $vars = Mailcode::create()->findVariables($this->tokenized)->getGroupedByHash();
        
        foreach($vars as $var)
        {
            $this->registerToken('Variable', $var->getMatchedText(), $var);
        }
    }
    
    protected function tokenize_operands() : void
    {
        foreach($this->operands as $operand)
        {
            if(strstr($this->tokenized, $operand))
            {
                $this->registerToken('Operand', $operand);
            }
        }
    }
    
    protected function tokenize_string_literals() : void
    {
        $matches = array();
        preg_match_all('/"(.*)"/sxU', $this->tokenized, $matches, PREG_PATTERN_ORDER);
        
        foreach($matches[0] as $match)
        {
            $this->registerToken('StringLiteral', $match);
        }
    }
    
    protected function tokenize_numbers() : void
    {
        $matches = array();
        preg_match_all('/-*[0-9]+\s*[.,]\s*[0-9]+|-*[0-9]+/sx', $this->tokenized, $matches, PREG_PATTERN_ORDER);
        
        foreach($matches[0] as $match)
        {
            $this->registerToken('Number', $match);
        }
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
}
