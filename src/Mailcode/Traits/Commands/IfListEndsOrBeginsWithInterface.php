<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface extends Mailcode_Interfaces_Commands_IfContains
{
    public const SEARCH_POSITION_BEGINNING = 'beginning';
    public const SEARCH_POSITION_END = 'end';

    /**
     * Retrieves the position in which to search for the search term(s).
     *
     * @return string
     *
     * @see Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface::SEARCH_POSITION_BEGINNING
     * @see Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface::SEARCH_POSITION_END
     */
    public function getSearchPosition() : string;

    public function isBeginsWith() : bool;

    public function isEndsWith() : bool;
}
