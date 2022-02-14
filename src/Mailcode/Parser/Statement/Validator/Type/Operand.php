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
class Mailcode_Parser_Statement_Validator_Type_Operand extends Mailcode_Parser_Statement_Validator_TokenType
{
    public const ERROR_NO_OPERAND_TOKEN_FOUND = 62901;
    
   /**
    * @var string
    */
    private $sign = '';
    
    protected function getTokenClass() : string
    {
        return Mailcode_Parser_Statement_Tokenizer_Token_Operand::class;
    }
    
    public function setOperandSign(string $sign) : Mailcode_Parser_Statement_Validator_Type_Operand
    {
        $this->sign = $sign;
        
        return $this;
    }
    
    protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool
    {
        if(!empty($this->sign) && $token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand)
        {
            return $token->getSign() === $this->sign;
        }
        
        return true;
    }
    
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_Token_Operand
    {
        if($this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand)
        {
            return $this->token;
        }
        
        throw new Mailcode_Exception(
            'No operand token found.',
            'Cannot retrieve operand token, none was found in the command. To avoid this error, do not use the getToken command when the command is invalid.',
            self::ERROR_NO_OPERAND_TOKEN_FOUND
        );
    }
    
    public function getSign() : string
    {
        return $this->getToken()->getSign();
    }
}
