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
interface Mailcode_Commands_Command_Type_Sibling extends Mailcode_Commands_Command_Type
{
    public function getParentName() : string;

    /**
     * Registers the command's opening command. This is called
     * automatically by the parser, and must not be used manually.
     *
     * @param Mailcode_Commands_Command_Type_Opening $opening
     */
    public function registerOpening(Mailcode_Commands_Command_Type_Opening $opening) : void;

    /**
     * Retrieves this command's opening command. Can be null
     * if there are nesting errors in the mailcode.
     *
     * @return Mailcode_Commands_Command_Type_Opening|null
     */
    public function getOpeningCommand() : ?Mailcode_Commands_Command_Type_Opening;

    /**
     * Retrieves all other sibling commands next to this one, if any.
     * @return Mailcode_Commands_Command_Type_Sibling[]
     */
    public function getSiblingCommands() : array;
}
