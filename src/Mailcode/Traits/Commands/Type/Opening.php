<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_Traits_Commands_Type_Opening}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Traits_Commands_Type_Opening
 */

declare(strict_types=1);

namespace Mailcode;

/**
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Commands_Command_Type_Opening
 */
trait Mailcode_Traits_Commands_Type_Opening
{
    /**
     * @var Mailcode_Commands_Command_Type_Closing|NULL
     */
    protected $closingCommand = null;

    /**
     * @var Mailcode_Commands_Command_Type_Sibling[]
     */
    protected $siblingCommands = array();

    public function registerClosing(Mailcode_Commands_Command_Type_Closing $closing) : void
    {
        $this->closingCommand = $closing;
    }

    public function registerSibling(Mailcode_Commands_Command_Type_Sibling $sibling) : void
    {
        $this->siblingCommands[] = $sibling;
    }

    public function getClosingCommand() : ?Mailcode_Commands_Command_Type_Closing
    {
        return $this->closingCommand;
    }

    /**
     * @return Mailcode_Commands_Command_Type_Sibling[]
     */
    public function getSiblingCommands() : array
    {
        return $this->siblingCommands;
    }
}
