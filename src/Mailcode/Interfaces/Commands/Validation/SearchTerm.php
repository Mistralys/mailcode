<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_SearchTerm
{
    const VALIDATION_NAME_SEARCH_TERM = 'search_term';

    public function getSearchTerm() : string;
}
