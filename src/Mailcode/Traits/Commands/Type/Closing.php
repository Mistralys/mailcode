<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_Traits_Commands_Type_Closing}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Traits_Commands_Type_Closing
 */

declare(strict_types=1);

namespace Mailcode;

/**
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Commands_Command_Type_Closing
 */
trait Mailcode_Traits_Commands_Type_Closing
{
    /**
     * @var Mailcode_Commands_Command_Type_Opening|NULL
     */
    protected $openingCommand = null;

    /**
     * Registers the command's opening command. This is called
     * automatically by the parser, and must not be used manually.
     *
     * @param Mailcode_Commands_Command_Type_Opening $opening
     */
    public function registerOpening(Mailcode_Commands_Command_Type_Opening $opening) : void
    {
        $this->openingCommand = $opening;
    }

    /**
     * Retrieves this command's opening command. Can be null
     * if there are nesting errors in the mailcode.
     *
     * @return Mailcode_Commands_Command_Type_Opening|null
     */
    public function getOpeningCommand() : ?Mailcode_Commands_Command_Type_Opening
    {
        return $this->openingCommand;
    }
}
