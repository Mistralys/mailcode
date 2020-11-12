<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_IfEndsOrBeginsWith
    extends
    Mailcode_Interfaces_Commands_Variable,
    Mailcode_Interfaces_Commands_CaseSensitive,
    Mailcode_Interfaces_Commands_SearchTerm
{
}
