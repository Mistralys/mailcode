<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_ListNotContains} class.
 *
 * @see Mailcode_Commands_Command_If_ListNotContains
 * @subpackage Commands
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF LIST NOT CONTAINS statement.
 * 
 * Checks if a list variable value does not contains a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_ListNotContains
    extends
        Mailcode_Commands_Command_If_NotContains
    implements
        Mailcode_Interfaces_Commands_ListVariables,
        Mailcode_Interfaces_Commands_Validation_ListPropertyVariable,
        Mailcode_Interfaces_Commands_Validation_RegexEnabled
{
    use Mailcode_Traits_Commands_ListVariables;
    use Mailcode_Traits_Commands_Validation_ListPropertyVariable;
    use Mailcode_Traits_Commands_Validation_RegexEnabled;

    protected function getValidations(): array
    {
        $validations = parent::getValidations();
        $validations[] = Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::VALIDATION_NAME_LIST_PROP_VARIABLE;
        $validations[] = Mailcode_Interfaces_Commands_Validation_RegexEnabled::VALIDATION_NAME_REGEX_ENABLED;

        return $validations;
    }

    protected function _collectListVariables(Mailcode_Variables_Collection_Regular $collection): void
    {
        $collection->add($this->getListVariable());
    }
}
