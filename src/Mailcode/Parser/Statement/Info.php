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
class Mailcode_Parser_Statement_Info
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer
    */
    protected $tokenizer;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    protected $tokens = array();
    
    public function __construct(Mailcode_Parser_Statement_Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
        $this->tokens = $this->tokenizer->getTokens(); 
    }
    
   /**
    * Whether the whole statement is a variable being assigned a value.
    * 
    * @return bool
    */
    public function isVariableAssignment() : bool
    {
        $variable = $this->getVariableByIndex(0);
        $operand = $this->getOperandByIndex(1);
        $value = $this->getTokenByIndex(2);
        
        if($variable && $operand && $value && $operand->isAssignment())
        {
            return true;
        }
        
        return false;
    }
    
   /**
    * Whether the whole statement is a variable being compared to something.
    * 
    * @return bool
    */
    public function isVariableComparison() : bool
    {
        $variable = $this->getVariableByIndex(0);
        $operand = $this->getOperandByIndex(1);
        $value = $this->getTokenByIndex(2);
        
        if($variable && $operand && $value && $operand->isComparator())
        {
            return true;
        }
        
        return false;
    }
    
   /**
    * Retrieves all variables used in the statement.
    * 
    * @return \Mailcode\Mailcode_Variables_Variable[]
    */
    public function getVariables()
    {
        $result = array();
        
        foreach($this->tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
            {
                $result[] = $token->getVariable();
            }
        }
        
        return $result;
    }
    
   /**
    * Retrieves a variable by its position in the command's parameters.
    * Returns null if there is no parameter at the specified index, or
    * if it is of another type.
    * 
    * @param int $index Zero-based index.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    public function getVariableByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_Variable
    {
        $token = $this->getTokenByIndex($index);
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $token;
        }
        
        return null;
    }
    
   /**
    * Retrieves a string literal by its position in the command's parameters.
    * Returns null if there is no parameter at the specified index, or
    * if it is of another type.
    *
    * @param int $index Zero-based index.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
    */
    public function getStringLiteralByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $token = $this->getTokenByIndex($index);
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return $token;
        }
        
        return null;
    }
    
   /**
    * Retrieves a keyword by its position in the command's parameters.
    * Returns null if there is no parameter at the specified index, or
    * if it is of another type.
    *
    * @param int $index Zero-based index.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
    */
    public function getKeywordByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        $token = $this->getTokenByIndex($index);
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $token;
        }
        
        return null;
    }
    
   /**
    * Retrieves an operand by its position in the command's parameters.
    * Returns null if there is no parameter at the specified index, or
    * if it is of another type.
    *
    * @param int $index Zero-based index.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_Operand|NULL
    */
    public function getOperandByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_Operand
    {
        $token = $this->getTokenByIndex($index);
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand)
        {
            return $token;
        }
        
        return null;
    }
    
   /**
    * Retrieves a parameter token by its position in the command's parameters,
    * regardless of its type. Returns null if there is no parameter at the 
    * specified index.
    *
    * @param int $index Zero-based index.
    * @return Mailcode_Parser_Statement_Tokenizer_Token|NULL
    */
    public function getTokenByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        if(isset($this->tokens[$index]))
        {
            return $this->tokens[$index];
        }
        
        return null;
    }
    
    public function hasTokenAtIndex(int $index) : bool
    {
        return isset($this->tokens[$index]);
    }
    
   /**
    * Retrieves all tokens.
    * @return Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    public function getTokens() : array
    {
        return $this->tokens;
    }
    
   /**
    * Retrieves all string literals that were found in the command.
    * @return \Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
    */
    public function getStringLiterals()
    {
        $result = array();
        
        foreach($this->tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
            {
                $result[] = $token;
            }
        }
        
        return $result;
    }
    
    public function createPruner() : Mailcode_Parser_Statement_Info_Pruner
    {
        return new Mailcode_Parser_Statement_Info_Pruner($this);
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword[]
     */
    public function getKeywords() : array
    {
        $result = array();

        foreach($this->tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
            {
                $result[] = $token;
            }
        }

        return $result;
    }

    /**
     * Adds or removes a keyword dependin on whether it should be enabled.
     *
     * @param string $keyword The keyword name, with or without :
     * @param bool $enabled
     * @return Mailcode_Parser_Statement_Info
     * @throws Mailcode_Exception
     */
    public function setKeywordEnabled(string $keyword, bool $enabled) : Mailcode_Parser_Statement_Info
    {
        if($enabled)
        {
            return $this->addKeyword($keyword);
        }

        return $this->removeKeyword($keyword);
    }

    /**
     * Adds a keyword to the command.
     *
     * @param string $keyword Keyword name, with or without :
     * @return $this
     * @throws Mailcode_Exception
     */
    protected function addKeyword(string $keyword) : Mailcode_Parser_Statement_Info
    {
        $keyword = rtrim($keyword, ':').':';

        if(!$this->hasKeyword($keyword))
        {
            $this->tokenizer->appendKeyword($keyword);
            $this->tokens = $this->tokenizer->getTokens();
        }

        return $this;
    }

    /**
     * Removes a keyword from the command, if it has one.
     * Has no effect otherwise.
     *
     * @param string $keyword Keyword name, with or without :
     * @return $this
     */
    public function removeKeyword(string $keyword) : Mailcode_Parser_Statement_Info
    {
        $keyword = rtrim($keyword, ':').':';
        $keywords = $this->getKeywords();

        foreach ($keywords as $kw)
        {
            if ($kw->getKeyword() !== $keyword) {
                continue;
            }

            $this->tokenizer->removeToken($kw);
            $this->tokens = $this->tokenizer->getTokens();
        }

        return $this;
    }

    /**
     * Whether the command has the specified keyword.
     *
     * @param string $keyword Keyword name, with or without :
     * @return bool
     */
    public function hasKeyword(string $keyword) : bool
    {
        $keyword = rtrim($keyword, ':').':';
        $keywords = $this->getKeywords();

        foreach ($keywords as $kw)
        {
            if($kw->getKeyword() === $keyword)
            {
                return true;
            }
        }

        return false;
    }
}
