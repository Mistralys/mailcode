<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_ListPropertyVariable}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_ListPropertyVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_ListPropertyVariable
 */
interface Mailcode_Interfaces_Commands_Validation_ListPropertyVariable extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_LIST_PROP_VARIABLE = 'list_property_variable';

    public const VALIDATION_NOT_A_LIST_PROPERTY = 77101;

    public const ERROR_NO_LIST_VARIABLE_PRESENT = 77201;

    public function getListVariable() : Mailcode_Variables_Variable;
    public function getListProperty() : Mailcode_Variables_Variable;
}
