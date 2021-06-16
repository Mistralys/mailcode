<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Value
{
    const VALIDATION_NAME = 'value';

    public function getValue() : string;
}
