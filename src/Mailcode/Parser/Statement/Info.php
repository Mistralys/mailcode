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

    /**
     * @var Mailcode_Parser_Statement_Info_Keywords
     */
    protected $keywords;

    /**
     * @var Mailcode_Parser_Statement_Info_Variables
     */
    protected $variables;

    public function __construct(Mailcode_Parser_Statement_Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
        $this->keywords = new Mailcode_Parser_Statement_Info_Keywords($this, $this->tokenizer);
        $this->variables = new Mailcode_Parser_Statement_Info_Variables($this, $this->tokenizer);
    }
    
   /**
    * Whether the whole statement is a variable being assigned a value.
    * 
    * @return bool
    */
    public function isVariableAssignment() : bool
    {
        return $this->variables->isAssignment();
    }
    
   /**
    * Whether the whole statement is a variable being compared to something.
    * 
    * @return bool
    */
    public function isVariableComparison() : bool
    {
        return $this->variables->isComparison();
    }

    /**
     * Retrieves all variables used in the statement.
     *
     * @return Mailcode_Variables_Variable[]
     * @throws Mailcode_Parser_Exception
     */
    public function getVariables() : array
    {
        return $this->variables->getAll();
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
        return $this->variables->getByIndex($index);
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
        return $this->keywords->getByIndex($index);
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
        $tokens = $this->tokenizer->getTokens();

        if(isset($tokens[$index]))
        {
            return $tokens[$index];
        }
        
        return null;
    }
    
    public function hasTokenAtIndex(int $index) : bool
    {
        $tokens = $this->tokenizer->getTokens();

        return isset($tokens[$index]);
    }
    
   /**
    * Retrieves all tokens.
    * @return Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    public function getTokens() : array
    {
        return $this->tokenizer->getTokens();
    }
    
   /**
    * Retrieves all string literals that were found in the command.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
    */
    public function getStringLiterals() : array
    {
        $result = array();
        $tokens = $this->tokenizer->getTokens();
        
        foreach($tokens as $token)
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
        return $this->keywords->getAll();
    }

    public function getKeywordsCollection() : Mailcode_Parser_Statement_Info_Keywords
    {
        return $this->keywords;
    }

    /**
     * Adds or removes a keyword depending on whether it should be enabled.
     *
     * @param string $keyword The keyword name, with or without :
     * @param bool $enabled
     * @return Mailcode_Parser_Statement_Info
     * @throws Mailcode_Parser_Exception
     */
    public function setKeywordEnabled(string $keyword, bool $enabled) : Mailcode_Parser_Statement_Info
    {
        $this->keywords->setEnabled($keyword, $enabled);

        return $this;
    }

    /**
     * Adds a keyword to the command.
     *
     * @param string $keyword Keyword name, with or without :
     * @return $this
     * @throws Mailcode_Parser_Exception
     */
    public function addKeyword(string $keyword) : Mailcode_Parser_Statement_Info
    {
        $this->keywords->add($keyword);

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
        $this->keywords->remove($keyword);

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
        return $this->keywords->hasKeyword($keyword);
    }

    public function removeToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->tokenizer->removeToken($token);
    }

    public function addStringLiteral(string $text) : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->tokenizer->appendStringLiteral($text);
    }

    public function prependStringLiteral(string $text) : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->tokenizer->prependStringLiteral($text);
    }
}
