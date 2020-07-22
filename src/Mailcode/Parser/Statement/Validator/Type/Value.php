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
class Mailcode_Parser_Statement_Validator_Type_Value extends Mailcode_Parser_Statement_Validator_TokenType
{
    const ERROR_NO_VALUE_TOKEN_FOUND = 62601;
    
    protected function getTokenClass() : string
    {
        return Mailcode_Parser_Statement_Tokenizer_ValueInterface::class;
    }
    
    protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool
    {
        return true;
    }
    
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_ValueInterface
    {
        if($this->token instanceof Mailcode_Parser_Statement_Tokenizer_ValueInterface)
        {
            return $this->token;
        }
        
        throw new Mailcode_Exception(
            'No variable token found.',
            'Cannot retrieve variable token, none was found in the command. To avoid this error, do not use the getToken command when the command is invalid.',
            self::ERROR_NO_VALUE_TOKEN_FOUND
        );
    }
}
