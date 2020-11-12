<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_BeginsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_BeginsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF BEGINS WITH statement.
 *
 * Checks if a variable value begins with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_BeginsWith
    extends Mailcode_Commands_Command_If
    implements Mailcode_Interfaces_Commands_IfEndsOrBeginsWith
{
    use Mailcode_Traits_Commands_IfEndsOrBeginsWith;
}
