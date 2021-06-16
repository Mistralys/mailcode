<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_SearchTerm
{
    const VALIDATION_NAME = 'search_term';

    public function getSearchTerm() : string;
}
