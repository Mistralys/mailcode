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
class Mailcode_Parser_Statement_Validator_Type_Variable extends Mailcode_Parser_Statement_Validator_TokenType
{
    const ERROR_NO_VARIABLE_TOKEN_FOUND = 62601;
    
    protected function getTokenClass() : string
    {
        return Mailcode_Parser_Statement_Tokenizer_Token_Variable::class;
    }
    
    protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool
    {
        return true;
    }
    
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_Token_Variable
    {
        if($this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $this->token;
        }
        
        throw new Mailcode_Exception(
            'No variable token found.',
            'Cannot retrieve variable token, none was found in the command. To avoid this error, do not use the getToken command when the command is invalid.',
            self::ERROR_NO_VARIABLE_TOKEN_FOUND
        );
    }

    /**
     * Retrieves the full name of the variable to show.
     *
     * NOTE: Only available once the command has been
     * validated. Always use isValid() first.
     *
     * @throws Mailcode_Exception
     * @return string
     */
    public function getVariableName() : string
    {
        return $this->getVariable()->getFullName();
    }

    /**
     * Retrieves the variable to show.
     *
     * NOTE: Only available once the command has been
     * validated. Always use isValid() first.
     *
     * @return Mailcode_Variables_Variable
     */
    public function getVariable() : Mailcode_Variables_Variable
    {
        return $this->getToken()->getVariable();
    }
}
