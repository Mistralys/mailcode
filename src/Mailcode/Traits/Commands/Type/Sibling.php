<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_Traits_Commands_Type_Sibling}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Traits_Commands_Type_Sibling
 */

declare(strict_types=1);

namespace Mailcode;

/**
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Commands_Command_Type_Sibling
 */
trait Mailcode_Traits_Commands_Type_Sibling
{
    /**
     * @var Mailcode_Commands_Command_Type_Opening|NULL
     */
    protected $openingCommand = null;

    public function registerOpening(Mailcode_Commands_Command_Type_Opening $opening) : void
    {
        $this->openingCommand = $opening;
    }

    public function getOpeningCommand() : ?Mailcode_Commands_Command_Type_Opening
    {
        return $this->openingCommand;
    }

    public function getSiblingCommands() : array
    {
        $opening = $this->getOpeningCommand();

        if($opening === null) {
            return array();
        }

        $siblings = $opening->getSiblingCommands();
        $result = array();
        foreach($siblings as $sibling)
        {
            if($sibling !== $this) {
                $result[] = $sibling;
            }
        }

        return $result;
    }
}
