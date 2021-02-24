<?php

declare(strict_types=1);

namespace Mailcode;

trait Mailcode_Traits_Commands_IfListEndsOrBeginsWith
{
    abstract public function getSearchPosition() : string;

    public function isBeginsWith() : bool
    {
        return $this->getSearchPosition() === Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface::SEARCH_POSITION_BEGINNING;
    }

    public function isEndsWith() : bool
    {
        return $this->getSearchPosition() === Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface::SEARCH_POSITION_END;
    }
}
