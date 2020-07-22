<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_EndsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_EndsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF ENDS WITH statement.
 *
 * Checks if a variable value ends with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_EndsWith extends Mailcode_Commands_Command_If
{
    use Mailcode_Traits_Commands_IfEndsOrBeginsWith;
}
