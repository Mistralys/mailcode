<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_IfVariable
    extends
    Mailcode_Interfaces_Commands_Validation_Operand,
    Mailcode_Interfaces_Commands_Validation_Value,
    Mailcode_Interfaces_Commands_Validation_Variable
{
}
