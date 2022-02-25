<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_Operand}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_Operand
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_Operand
 */
interface Mailcode_Interfaces_Commands_Validation_Operand extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_OPERAND = 'operand';

    public function getOperand() : Mailcode_Parser_Statement_Tokenizer_Token_Operand;
    public function getSign() : string;
}
