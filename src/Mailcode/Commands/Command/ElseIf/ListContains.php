<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_ListContains} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_ListContains
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening ELSE IF LIST CONTAINS statement.
 * 
 * Checks if a list variable value contains a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_ListContains
    extends
        Mailcode_Commands_Command_ElseIf_Contains
    implements
        Mailcode_Interfaces_Commands_ListVariables,
        Mailcode_Interfaces_Commands_ListPropertyVariable,
        Mailcode_Interfaces_Commands_RegexEnabled
{
    use Mailcode_Traits_Commands_ListVariables;
    use Mailcode_Traits_Commands_Validation_ListPropertyVariable;
    use Mailcode_Traits_Commands_Validation_RegexEnabled;

    protected function getValidations(): array
    {
        $validations = parent::getValidations();
        $validations[] = 'list_property_variable';
        $validations[] = 'regex_enabled';

        return $validations;
    }

    protected function _collectListVariables(Mailcode_Variables_Collection_Regular $collection): void
    {
        $collection->add($this->getListVariable());
    }
}
