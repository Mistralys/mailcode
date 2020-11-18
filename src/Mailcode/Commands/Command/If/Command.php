<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Command} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Command
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
class Mailcode_Commands_Command_If_Command extends Mailcode_Commands_Command_If
{
    public function hasFreeformParameters() : bool
    {
        return true;
    }
}
