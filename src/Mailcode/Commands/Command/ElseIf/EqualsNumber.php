<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_ElseIf_EqualsNumber} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_ElseIf_EqualsNumber
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_EqualsNumber extends Mailcode_Commands_Command_ElseIf
{
    use Mailcode_Traits_Commands_IfNumber;
}
