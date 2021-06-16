<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_IfEndsOrBeginsWith
    extends
    Mailcode_Interfaces_Commands_Validation_Variable,
    Mailcode_Interfaces_Commands_Validation_CaseSensitive,
    Mailcode_Interfaces_Commands_Validation_SearchTerm
{
}
