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

use AppUtils\ClassHelper;
use AppUtils\ClassHelper\BaseClassHelperException;
use Mailcode\Parser\Statement\Tokenizer\EventHandler;
use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

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
    public const ERROR_TOKENIZE_METHOD_MISSING = 49801;
    public const ERROR_INVALID_TOKEN_CREATED = 49802;
    public const ERROR_INVALID_TOKEN_CLASS = 49803;
    public const ERROR_TARGET_INSERT_TOKEN_NOT_FOUND = 49804;

    /**
     * @var string[]
     */
    protected array $tokenClasses = array(
        Mailcode_Parser_Statement_Tokenizer_Process_LegacySyntaxConversion::class,
        Mailcode_Parser_Statement_Tokenizer_Process_Variables::class,
        Mailcode_Parser_Statement_Tokenizer_Process_NormalizeQuotes::class,
        Mailcode_Parser_Statement_Tokenizer_Process_EncodeSpecialChars::class,
        Mailcode_Parser_Statement_Tokenizer_Process_Keywords::class,
        // Must be before named parameters to exclude equal signs in strings
        Mailcode_Parser_Statement_Tokenizer_Process_StringLiterals::class,
        // Must be before numbers, because named parameters can contain numbers
        Mailcode_Parser_Statement_Tokenizer_Process_NamedParameters::class,
        Mailcode_Parser_Statement_Tokenizer_Process_Numbers::class,
        Mailcode_Parser_Statement_Tokenizer_Process_Operands::class,
        Mailcode_Parser_Statement_Tokenizer_Process_ExtractTokens::class,
        // Must be at the end when all tokens have been determined
        Mailcode_Parser_Statement_Tokenizer_Process_SetNames::class,
    );
    
   /**
    * @var Mailcode_Parser_Statement
    */
    protected Mailcode_Parser_Statement $statement;
    
   /**
    * @var string
    */
    protected string $tokenized = '';
    
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    protected array $tokensOrdered = array();
    
   /**
    * @var string[]
    */
    protected static array $ids = array();

    /**
     * @var callable[]
     */
    protected array $changeHandlers = array();

    private EventHandler $eventHandler;

    public function __construct(Mailcode_Parser_Statement $statement)
    {
        $this->statement = $statement;
        $this->eventHandler = new EventHandler($this);

        $this->tokenize($statement->getStatementString());
    }

    public function getSourceCommand() : ?Mailcode_Commands_Command
    {
        return $this->statement->getSourceCommand();
    }

   /**
    * Retrieves all tokens detected in the statement string, in 
    * the order they were found.
    * 
    * @return Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    public function getTokens() : array
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
    public function getUnknown() : array
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
            
            if($string === '') {
                continue;
            }

            // Only add spaces between tokens if they require spacing
            if($token->hasSpacing()) {
                $string .= ' ';
            }

            $parts[] = $string;
        }
        
        return trim(implode('', $parts));
    }

    /**
     * Goes through all tokenization processors, in the order that
     * they are defined in the tokenCategories property. This filters
     * the statement string, and extracts the tokens contained within.
     *
     * @param string $statement
     *
     * @throws Mailcode_Parser_Exception
     *
     * @see Mailcode_Parser_Statement_Tokenizer_Process
     */
    protected function tokenize(string $statement) : void
    {
        $statement = trim($statement);
        $tokens = array();

        foreach($this->tokenClasses as $tokenClass)
        {
            $processor = $this->createProcessor($tokenClass, $statement, $tokens);
            $processor->process();

            $statement = $processor->getStatement();
            $tokens = $processor->getTokens();
        }

        $this->tokenized = $statement;
        $this->tokensOrdered = $tokens;
    }

    /**
     * @param string $className
     * @param string $statement
     * @param Mailcode_Parser_Statement_Tokenizer_Token[] $tokens
     * @return Mailcode_Parser_Statement_Tokenizer_Process
     * @throws Mailcode_Parser_Exception
     */
    protected function createProcessor(string $className, string $statement, array $tokens) : Mailcode_Parser_Statement_Tokenizer_Process
    {
        $instance = new $className($this, $statement, $tokens);

        if($instance instanceof Mailcode_Parser_Statement_Tokenizer_Process)
        {
            return $instance;
        }

        throw new Mailcode_Parser_Exception(
            'Unknown statement token.',
            sprintf(
                'The tokenize class [%s] does not extend the base process class.',
                $className
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

        $class = Mailcode_Parser_Statement_Tokenizer_Token::class.'_'.$type;

        $token = new $class($tokenID, $matchedText, $subject, $this->getSourceCommand());

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token)
        {
            return $token;
        }

        throw new Mailcode_Parser_Exception(
            'Invalid token class',
            sprintf(
                'The class [%s] does not extend the base token class.',
                get_class($token)
            ),
            self::ERROR_INVALID_TOKEN_CLASS
        );
    }

    private function createVariable(Mailcode_Variables_Variable $variable) : Mailcode_Parser_Statement_Tokenizer_Token_Variable
    {
        return ClassHelper::requireObjectInstanceOf(
            Mailcode_Parser_Statement_Tokenizer_Token_Variable::class,
            $this->createToken('Variable', dollarize($variable->getFullName()), $variable)
        );
    }

    private function createKeyword(string $name) : Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        $name = rtrim($name, ':').':';

        $token = $this->createToken('Keyword', $name);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $token;
        }

        throw new Mailcode_Parser_Exception(
            'Invalid token created',
            '',
            self::ERROR_INVALID_TOKEN_CREATED
        );
    }

    public function appendKeyword(string $name) : Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        $token = $this->createKeyword($name);

        $this->appendToken($token);

        return $token;
    }

    private function createStringLiteral(string $text) : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $token = $this->createToken('StringLiteral', SpecialChars::encodeAll($text));

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return $token;
        }

        throw new Mailcode_Parser_Exception(
            'Invalid token created',
            '',
            self::ERROR_INVALID_TOKEN_CREATED
        );
    }

    private function createNumber(string $number) : Mailcode_Parser_Statement_Tokenizer_Token_Number
    {
        $token = $this->createToken('Number', $number);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number)
        {
            return $token;
        }

        throw new Mailcode_Parser_Exception(
            'Invalid token created',
            '',
            self::ERROR_INVALID_TOKEN_CREATED
        );
    }

    public function appendStringLiteral(string $text) : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $token = $this->createStringLiteral($text);

        $this->appendToken($token);

        return $token;
    }

    public function appendNumber(string $number) : Mailcode_Parser_Statement_Tokenizer_Token_Number
    {
        $token = $this->createNumber($number);

        $this->appendToken($token);

        return $token;
    }

    public function prependStringLiteral(string $text) : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $token = $this->createStringLiteral($text);

        $this->prependToken($token);

        return $token;
    }

    public function removeToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : Mailcode_Parser_Statement_Tokenizer
    {
        $name = $this->findNameToken($token);
        if($name !== null) {
            $this->removeToken($name);
        }

        $keep = array();
        $tokenID = $token->getID();
        $removed = false;

        foreach ($this->tokensOrdered as $checkToken)
        {
            if($checkToken->getID() === $tokenID)
            {
                $removed = true;
                continue;
            }

            $keep[] = $checkToken;
        }

        $this->tokensOrdered = $keep;

        if($removed)
        {
            $this->eventHandler->handleTokenRemoved($token);
        }

        return $this;
    }

    /**
     * @param Mailcode_Parser_Statement_Tokenizer_Token $token
     * @return $this
     */
    protected function appendToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : self
    {
        $this->tokensOrdered[] = $token;

        $this->eventHandler->handleTokenAppended($token);

        return $this;
    }

    /**
     * @param Mailcode_Parser_Statement_Tokenizer_Token $token
     * @return $this
     */
    protected function prependToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : self
    {
        array_unshift($this->tokensOrdered, $token);

        $this->eventHandler->handleTokenPrepended($token);

        return $this;
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
    public function onTokensChanged(callable $callback) : void
    {
        $this->changeHandlers[] = $callback;
    }

    /**
     * @return EventHandler
     */
    public function getEventHandler() : EventHandler
    {
        return $this->eventHandler;
    }

    public function appendVariable(Mailcode_Variables_Variable $variable) : Mailcode_Parser_Statement_Tokenizer_Token_Variable
    {
        $token = $this->createVariable($variable);
        $this->appendToken($token);
        return $token;
    }

    public function findNameToken(Mailcode_Parser_Statement_Tokenizer_Token $targetToken) : ?Mailcode_Parser_Statement_Tokenizer_Token_ParamName
    {
        $targetID = $targetToken->getID();

        foreach($this->tokensOrdered as $idx => $token)
        {
            if($token->getID() === $targetID)
            {
                $prev = $this->tokensOrdered[$idx-1] ?? null;
                if($prev instanceof Mailcode_Parser_Statement_Tokenizer_Token_ParamName)
                {
                    return $prev;
                }
            }
        }

        return null;
    }

    /**
     * Injects a parameter name token into the statement, before
     * the target token. Existing parameter names are replaced.
     *
     * @param Mailcode_Parser_Statement_Tokenizer_Token $targetToken
     * @param string $name
     * @return Mailcode_Parser_Statement_Tokenizer_Token_ParamName
     *
     * @throws Mailcode_Parser_Exception {@see self::ERROR_TARGET_INSERT_TOKEN_NOT_FOUND}
     * @throws BaseClassHelperException
     */
    public function injectParamName(Mailcode_Parser_Statement_Tokenizer_Token $targetToken, string $name) : Mailcode_Parser_Statement_Tokenizer_Token_ParamName
    {
        $existing = $this->findNameToken($targetToken);
        if($existing) {
            $this->removeToken($existing);
        }

        $nameToken = ClassHelper::requireObjectInstanceOf(
            Mailcode_Parser_Statement_Tokenizer_Token_ParamName::class,
            $this->createToken('ParamName', $name.'=')
        );

        $this->insertBefore($targetToken, $nameToken);
        $targetToken->registerNameToken($nameToken);

        return $nameToken;
    }

    public function insertBefore(Mailcode_Parser_Statement_Tokenizer_Token $targetToken, Mailcode_Parser_Statement_Tokenizer_Token $newToken) : self
    {
        $targetID = $targetToken->getID();
        $tokens = array();
        $found = false;

        foreach($this->tokensOrdered as $token)
        {
            if($token->getID() === $targetID)
            {
                $tokens[] = $newToken;
                $found = true;
            }

            $tokens[] = $token;
        }

        if($found) {
            $this->tokensOrdered = $tokens;

            return $this;
        }

        throw new Mailcode_Parser_Exception(
            'Could not find target token for insertion.',
            sprintf(
                'The token [%s] was not found in the statement [%s].',
                $targetToken->getNormalized(),
                $this->getNormalized()
            ),
            self::ERROR_TARGET_INSERT_TOKEN_NOT_FOUND
        );
    }

    private function dumpTokens() : void
    {
        echo PHP_EOL;
        echo 'Statement: ['.$this->getNormalized().']'.PHP_EOL;
        echo 'Tokens:'.PHP_EOL;

        foreach($this->tokensOrdered as $idx => $token)
        {
            echo '- #'.$idx.' '.$token->getID().''.PHP_EOL;
            echo '     Type: '.$token->getTypeID().PHP_EOL;
            echo '     Normalized: ['.$token->getNormalized().']'.PHP_EOL;
        }
    }
}
