<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Contains} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Contains
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF CONTAINS statement.
 * 
 * Checks if a variable value contains a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_Contains
    extends Mailcode_Commands_Command_If
    implements Mailcode_Interfaces_Commands_IfContains
{
    use Mailcode_Traits_Commands_IfContains;
}
