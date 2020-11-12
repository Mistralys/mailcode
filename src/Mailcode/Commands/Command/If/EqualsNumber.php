<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_If_EqualsNumber} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_If_EqualsNumber
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
class Mailcode_Commands_Command_If_EqualsNumber extends Mailcode_Commands_Command_If
{
    use Mailcode_Traits_Commands_IfNumber;
}
