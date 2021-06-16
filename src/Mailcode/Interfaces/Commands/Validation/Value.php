<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_Value
{
    const VALIDATION_NAME_VALUE = 'value';

    public function getValue() : string;
}
