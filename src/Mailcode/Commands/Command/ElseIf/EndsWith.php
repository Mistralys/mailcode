<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_EndsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_EndsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening ELSE IF ENDS WITH statement.
 *
 * Checks if a variable value ends with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_EndsWith
    extends Mailcode_Commands_Command_ElseIf
    implements Mailcode_Interfaces_Commands_IfEndsOrBeginsWith
{
    use Mailcode_Traits_Commands_IfEndsOrBeginsWith;
}
