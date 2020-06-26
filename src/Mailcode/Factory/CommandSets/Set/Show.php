<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_Show} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory_CommandSets_Set_Show
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command set used to create showxxx commands.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_Show extends Mailcode_Factory_CommandSets_Set
{
    public function showVar(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowVariable',
            '',
            $variableName,
            '{showvar:'.$variableName.'}'
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowVariable)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ShowVariable', $cmd);
    }
    
    public function showDate(string $variableName, string $formatString="") : Mailcode_Commands_Command_ShowDate
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        
        $format = '';
        if(!empty($formatString))
        {
            $format = sprintf(
                ' "%s"',
                $formatString
            );
        }
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowDate',
            '',
            $variableName.$format,
            sprintf(
                '{showdate: %s%s}',
                $variableName,
                $format
            )
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowDate)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ShowDate', $cmd);
    }
    
    public function showSnippet(string $snippetName) : Mailcode_Commands_Command_ShowSnippet
    {
        $snippetName = $this->instantiator->filterVariableName($snippetName);
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowSnippet',
            '',
            $snippetName,
            '{showsnippet:'.$snippetName.'}'
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowSnippet)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ShowSnippet', $cmd);
    }
}
