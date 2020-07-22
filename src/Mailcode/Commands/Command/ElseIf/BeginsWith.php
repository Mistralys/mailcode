<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_BeginsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_BeginsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening ELSE IF BEGINS WITH statement.
 *
 * Checks if a variable value begins with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_BeginsWith extends Mailcode_Commands_Command_If
{
    use Mailcode_Traits_Commands_IfEndsOrBeginsWith;
}
