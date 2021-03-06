<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_ListPropertyVariable
{
    const VALIDATION_NAME_LIST_PROP_VARIABLE = 'list_property_variable';

    const VALIDATION_NOT_A_LIST_PROPERTY = 77101;

    const ERROR_NO_LIST_VARIABLE_PRESENT = 77201;

    public function getListVariable() : Mailcode_Variables_Variable;
    public function getListProperty() : Mailcode_Variables_Variable;
}
