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

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_Set extends Mailcode_Factory_CommandSets_Set
{
    public function var(string $variableName, string $value, bool $quoteValue=true) : Mailcode_Commands_Command_SetVariable
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        
        if($quoteValue)
        {
            $value = $this->instantiator->quoteString($value);
        }
        
        $params = $variableName.' = '.$value;
        
        $cmd = $this->commands->createCommand(
            'SetVariable',
            '', // type
            $params,
            '{setvar: '.$params.'}'
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_SetVariable)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('SetVariable', $cmd);
    }

    /**
     * Treats the value as a string literal, so automatically adds quotes around it.
     *
     * @param string $variableName
     * @param string $value
     * @return Mailcode_Commands_Command_SetVariable
     * @throws Mailcode_Factory_Exception
     */
    public function varString(string $variableName, string $value) : Mailcode_Commands_Command_SetVariable
    {
        return $this->var($variableName, $value, true);
    }
}
