<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_NotEmpty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_NotEmpty
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening ELSE IF EMPTY statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_NotEmpty
    extends Mailcode_Commands_Command_ElseIf
    implements Mailcode_Interfaces_Commands_IfNotEmpty
{
    use Mailcode_Traits_Commands_IfNotEmpty;
}
