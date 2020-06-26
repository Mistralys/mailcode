<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_NotEmpty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_NotEmpty
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
class Mailcode_Commands_Command_If_NotEmpty extends Mailcode_Commands_Command_If
{
    use Mailcode_Traits_Commands_IfNotEmpty;
}
