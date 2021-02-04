<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_ListNotContains} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_ListNotContains
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
class Mailcode_Commands_Command_If_ListNotContains extends Mailcode_Commands_Command_If_NotContains implements Mailcode_Interfaces_Commands_ListVariables, Mailcode_Interfaces_Commands_ListPropertyVariable
{
    use Mailcode_Traits_Commands_ListVariables;
    use Mailcode_Traits_Commands_Validation_ListPropertyVariable;

    protected function getValidations(): array
    {
        $validations = parent::getValidations();
        $validations[] = 'list_property_variable';

        return $validations;
    }

    protected function _collectListVariables(Mailcode_Variables_Collection_Regular $collection): void
    {
        $collection->add($this->getListVariable());
    }
}
