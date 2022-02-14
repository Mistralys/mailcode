<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Validator_Type_Variable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Parser_Statement_Validator_Type_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to validate a single variable token in a command's parameters.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Statement_Validator_TokenType extends Mailcode_Parser_Statement_Validator_Type
{
    public const VALIDATION_ERROR_COMMAND_WITHOUT_PARAMETERS = 62401;
    
   /**
    * @var integer
    */
    protected $searchIndex = -1;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token|NULL
    */
    protected $token;
    
   /**
    * Retrieves the class name of the token type to 
    * limit the token search to. Only a token of this
    * type will be stored.
    * 
    * @return string
    */
    abstract protected function getTokenClass() : string;
    
   /**
    * Allows the validator to apply custom search
    * criteria to the tokens that are searched 
    * through to find the target token.
    * 
    * NOTE: Should return true if there are no
    * custom criteria to apply.
    * 
    * @param Mailcode_Parser_Statement_Tokenizer_Token $token
    * @return bool
    */
    abstract protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool;
    
   /**
    * @return Mailcode_Parser_Statement_Tokenizer_Token
    */
    abstract public function getToken();
    
   /**
    * Checks if the specified token matches the current
    * search criteria (index, type...). If the token is
    * a match, the `_isMatch()` method is called to allow
    * the validator class to apply some custom criteria. 
    * 
    * @param Mailcode_Parser_Statement_Tokenizer_Token $token
    * @param int $index
    * @return bool
    */
    protected function isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token, int $index) : bool
    {
        // filter out tokens of other types
        if(!is_a($token, $this->getTokenClass()))
        {
            return false;
        }
        
        if($this->searchIndex >= 0 && $index !== $this->searchIndex)
        {
            return false;
        }
        
        return $this->_isMatch($token);
    }
    
   /**
    * Attempts to find a token in the statement
    * that matches the current criteria, and the
    * first one it finds is returned.
    * 
    * @return Mailcode_Parser_Statement_Tokenizer_Token|NULL
    */
    protected function findToken() : ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        $tokens = $this->params->getTokens();
        
        foreach($tokens as $index => $token)
        {
            if($this->isMatch($token, $index))
            {
                return $token;
            }
        }
        
        return null;
    }
    
    protected function _validate() : bool
    {
        $token = $this->findToken();
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token && is_a($token, $this->getTokenClass()))
        {
            $this->token = $token;
            return true;
        }
        
        return false;
    }
    
   /**
    * Searches for a specific token index.
    * 
    * NOTE: only relevant when trying to find a single token.
    * 
    * @param int $index
    * @return $this
    */
    public function setIndex(int $index) : Mailcode_Parser_Statement_Validator_TokenType
    {
        $this->searchIndex = $index;
        
        return $this;
    }
}
