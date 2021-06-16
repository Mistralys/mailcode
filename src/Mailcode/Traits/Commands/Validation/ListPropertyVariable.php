<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: used by commands that accept
 * a single list variable property. Ensures that the specified
 * variable uses the scheme `$LIST.PROPERTY`, and not a single
 * path variable like `$FOO`.
 *
 * @see Mailcode_Interfaces_Commands_Validation_ListPropertyVariable
 *@subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 * @property OperationResult $validationResult
 *
 * @package Mailcode
 */
trait Mailcode_Traits_Commands_Validation_ListPropertyVariable
{
    /**
     * @var Mailcode_Variables_Variable|null
     */
    protected $listVariable = null;

    abstract public function getVariable() : Mailcode_Variables_Variable;

    protected function validateSyntax_list_property_variable() : void
    {
        $var = $this->getVariable();

        // Split the variable name by the dot
        $parts = explode('.', ltrim($var->getFullName(), '$'));

        if(count($parts) !== 2)
        {
            $this->validationResult->makeError(
                t('The variable %1$s is not a list property:', '<code>'.$var->getFullName().'</code>').' '.
                t('Expected a name with a dot, like %1$s', '<code>$'.t('LIST.PROPERTY').'</code>'),
                Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::VALIDATION_NOT_A_LIST_PROPERTY
            );

            return;
        }

        // Create the list variable from the path of the property variable.
        $this->listVariable = new Mailcode_Variables_Variable(
            '',
            $parts[0],
            '$'.$parts[0],
            $var->getSourceCommand()
        );
    }

    /**
     * Retrieves the specified list variable, if any.
     * If the command is erroneous no list variable may
     * be present, in which case an exception is thrown.
     *
     * @return Mailcode_Variables_Variable
     * @throws Mailcode_Exception
     *
     * @see Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::ERROR_NO_LIST_VARIABLE_PRESENT
     */
    public function getListVariable() : Mailcode_Variables_Variable
    {
        if(isset($this->listVariable))
        {
            return $this->listVariable;
        }

        throw new Mailcode_Exception(
            'No list variable present.',
            '',
            Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::ERROR_NO_LIST_VARIABLE_PRESENT
        );
    }

    /**
     * @return Mailcode_Variables_Variable
     * @throws Mailcode_Exception
     */
    public function getListProperty() : Mailcode_Variables_Variable
    {
        return $this->getVariable();
    }
}
