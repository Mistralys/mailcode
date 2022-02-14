<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_Variable
{
    public const VALIDATION_NAME_VARIABLE = 'variable';

    public function getVariable() : Mailcode_Variables_Variable;
    public function getVariableName() : string;
    public function isInLoop() : bool;
    public function getLoopCommand() : ?Mailcode_Commands_Command_For;
}
