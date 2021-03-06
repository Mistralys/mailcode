<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Empty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Empty
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
class Mailcode_Commands_Command_If_Empty
    extends Mailcode_Commands_Command_If
    implements Mailcode_Interfaces_Commands_Validation_IfEmpty
{
    use Mailcode_Traits_Commands_IfEmpty;
}
