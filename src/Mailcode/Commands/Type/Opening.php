<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Type_Opening} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Type_Opening
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
interface Mailcode_Commands_Command_Type_Opening extends Mailcode_Commands_Command_Type
{
    /**
     * Registers the command's closing command. This is called
     * automatically by the parser, and must not be used manually.
     *
     * @param Mailcode_Commands_Command_Type_Closing $closing
     */
    public function registerClosing(Mailcode_Commands_Command_Type_Closing $closing) : void;

    /**
     * Registers any sibling commands next to this command. This is called
     * automatically by the parser, and must not be used manually.
     *
     * @param Mailcode_Commands_Command_Type_Sibling $sibling
     */
    public function registerSibling(Mailcode_Commands_Command_Type_Sibling $sibling) : void;

    /**
     * Retrieves the command's closing command, if any. Can be null if the
     * mailcode has nesting errors.
     *
     * @return Mailcode_Commands_Command_Type_Closing|null
     */
    public function getClosingCommand() : ?Mailcode_Commands_Command_Type_Closing;

    /**
     * Retrieves all sibling commands next to this command.
     *
     * @return Mailcode_Commands_Command_Type_Sibling[]
     */
    public function getSiblingCommands() : array;
}
