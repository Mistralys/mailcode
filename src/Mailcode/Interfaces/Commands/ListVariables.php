<?php

declare(strict_types=1);

namespace Mailcode;

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
