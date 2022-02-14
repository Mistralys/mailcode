<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_Variable} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing a variable name.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_Variable extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public const ERROR_NOT_A_VARIABLE_INSTANCE = 49501;
    
    public function getVariable() : Mailcode_Variables_Variable
    {
        if($this->subject instanceof Mailcode_Variables_Variable)
        {
            return $this->subject;
        }
        
        throw new Mailcode_Exception(
            'Subject is not a variable instance.',
            null,
            self::ERROR_NOT_A_VARIABLE_INSTANCE
        );
    }
    
    public function getNormalized() : string
    {
        return $this->getVariable()->getFullName();
    }
}
