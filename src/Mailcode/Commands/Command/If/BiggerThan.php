<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_BiggerThan} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_If_BiggerThan
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
class Mailcode_Commands_Command_If_BiggerThan extends Mailcode_Commands_Command_If
{
    use Mailcode_Traits_Commands_IfNumber;
}
