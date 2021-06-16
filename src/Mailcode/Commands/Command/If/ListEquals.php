<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_ListEquals} class.
 *
 * @see Mailcode_Commands_Command_If_ListEquals
 * @subpackage Commands
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF LIST EQUALS statement.
 *
 * Checks if a list variable value contains a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_ListEquals
    extends
        Mailcode_Commands_Command_If_Contains
    implements
        Mailcode_Interfaces_Commands_ListVariables,
        Mailcode_Interfaces_Commands_Validation_ListPropertyVariable
{

    use Mailcode_Traits_Commands_ListVariables;
    use Mailcode_Traits_Commands_Validation_ListPropertyVariable;

    protected function getValidations(): array
    {
        $validations = parent::getValidations();
        $validations[] = Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::VALIDATION_NAME_LIST_PROP_VARIABLE;

        return $validations;
    }

    protected function _collectListVariables(Mailcode_Variables_Collection_Regular $collection): void
    {
        $collection->add($this->getListVariable());
    }
}
