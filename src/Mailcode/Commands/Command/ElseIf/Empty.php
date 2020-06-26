<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_Empty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_Empty
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
class Mailcode_Commands_Command_ElseIf_Empty extends Mailcode_Commands_Command_ElseIf
{
    use Mailcode_Traits_Commands_IfEmpty;
}
