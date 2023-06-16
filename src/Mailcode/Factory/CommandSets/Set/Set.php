<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_Set} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see Mailcode_Factory_CommandSets_Set_Set
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ClassHelper;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_Set extends Mailcode_Factory_CommandSets_Set
{
    public function var(string $variableName, string $value, bool $quoteValue = true, bool $asCount = false): Mailcode_Commands_Command_SetVariable
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        $commandID = ClassHelper::getClassTypeName(Mailcode_Commands_Command_SetVariable::class);

        if ($asCount) {
            $params = $variableName . ' count: ' . $value;
        } else {
            if ($quoteValue) {
                $value = $this->instantiator->quoteString($value);
            }

            $params = $variableName . ' = ' . $value;
        }

        $cmd = $this->commands->createCommand(
            $commandID,
            '', // type
            $params,
            sprintf(
                '{%s: %s}',
                Mailcode_Commands_Command_SetVariable::COMMAND_NAME,
                $params
            )
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_SetVariable) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType($commandID, $cmd);
    }

    /**
     * Treats the value as a string literal, so automatically adds quotes around it.
     *
     * @param string $variableName
     * @param string $value
     * @return Mailcode_Commands_Command_SetVariable
     * @throws Mailcode_Factory_Exception
     */
    public function varString(string $variableName, string $value): Mailcode_Commands_Command_SetVariable
    {
        return $this->var($variableName, $value, true);
    }

    /**
     * Set a variable by counting the amount of entries in the
     * target list variable.
     *
     * @param string $variableName The name of the variable to store the value in.
     * @param string $listVariableName The name of the variable to count records of.
     * @return Mailcode_Commands_Command_SetVariable
     * @throws Mailcode_Factory_Exception
     */
    public function varCount(string $variableName, string $listVariableName) : Mailcode_Commands_Command_SetVariable
    {
        return $this->var(
            $variableName,
            dollarize($listVariableName),
            false,
            true
        );
    }
}
