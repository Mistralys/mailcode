<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Type_Closing} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Type_Closing
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Base structure for opening commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Commands_Command_Type_Closing extends Mailcode_Commands_Command_Type
{
    public function registerOpening(Mailcode_Commands_Command_Type_Opening $command) : void;

    public function getOpeningCommand() : ?Mailcode_Commands_Command_Type_Opening;
}
