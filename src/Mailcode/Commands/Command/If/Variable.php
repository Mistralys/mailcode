<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Variable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * IF for variable comparisons.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_Variable
    extends Mailcode_Commands_Command_If
    implements Mailcode_Interfaces_Commands_IfVariable
{
    use Mailcode_Traits_Commands_IfVariable;
}
