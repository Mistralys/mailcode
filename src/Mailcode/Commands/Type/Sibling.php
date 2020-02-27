<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Type_Sibling} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Type_Sibling
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
abstract class Mailcode_Commands_Command_Type_Sibling extends Mailcode_Commands_Command
{
    abstract public function getParentName() : string;
    
    public function getCommandType() : string
    {
        return 'Sibling';
    }
}
