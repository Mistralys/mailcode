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
class Mailcode_Parser_Statement_Validator_Type_StringLiteral extends Mailcode_Parser_Statement_Validator_TokenType
{
    public const ERROR_NO_STRING_TOKEN_FOUND = 62601;
    
    protected function getTokenClass() : string
    {
        return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class;
    }
    
    protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool
    {
        return true;
    }
    
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        if($this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return $this->token;
        }
        
        throw new Mailcode_Exception(
            'No string literal token found.',
            'Cannot retrieve string literal token, none was found in the command. To avoid this error, do not use the getToken command when the command is invalid.',
            self::ERROR_NO_STRING_TOKEN_FOUND
        );
    }
}
