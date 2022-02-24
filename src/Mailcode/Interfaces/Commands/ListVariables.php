<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_Interfaces_Commands_ListVariables}
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Interfaces_Commands_ListVariables
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for list variable commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Interfaces_Commands_ListVariables extends Mailcode_Interfaces_Commands_Command
{
    /**
     * Retrieves all variables that are lists.
     * @return Mailcode_Variables_Collection_Regular
     */
    public function getListVariables() : Mailcode_Variables_Collection_Regular;

    /**
     * Retrieves all variables of the command.
     * @return Mailcode_Variables_Collection_Regular
     */
    public function getVariables() : Mailcode_Variables_Collection_Regular;
}
