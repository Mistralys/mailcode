<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_SearchTerm
{
    public function getSearchTerm() : string;
}
