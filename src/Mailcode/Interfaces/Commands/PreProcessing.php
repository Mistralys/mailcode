<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_PreProcessing
{
    public function preProcessOpening() : string;

    public function preProcessClosing() : string;
}
