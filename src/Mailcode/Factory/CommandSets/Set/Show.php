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
    public function var(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        
        $cmd = $this->commands->createCommand(
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
    
    public function date(string $variableName, string $formatString="") : Mailcode_Commands_Command_ShowDate
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
        
        $cmd = $this->commands->createCommand(
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

    public function number(string $variableName, string $formatString="", bool $absolute=false) : Mailcode_Commands_Command_ShowNumber
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        $paramsString = $this->compileNumberParams($formatString, $absolute);

        $cmd = $this->commands->createCommand(
            'ShowNumber',
            '',
            $variableName.$paramsString,
            sprintf(
                '{shownumber: %s%s}',
                $variableName,
                $paramsString
            )
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowNumber)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowNumber', $cmd);
    }

    private function compileNumberParams(string $formatString="", bool $absolute=false) : string
    {
        $params = array();

        if(!empty($formatString))
        {
            $params[] = sprintf(
                ' "%s"',
                $formatString
            );
        }

        if($absolute)
        {
            $params[] = ' absolute:';
        }

        if(!empty($params))
        {
            return ' '.implode(' ', $params);
        }

        return '';
    }

    /**
     * Creates a `showphone` command.
     *
     * @param string $variableName The name of the variable, with or without $ sign.
     * @param string $sourceFormat Two-letter country code, case insensitive.
     * @param string $urlEncoding The URL encoding mode, if any.
     * @return Mailcode_Commands_Command_ShowPhone
     * @throws Mailcode_Exception
     * @throws Mailcode_Factory_Exception
     */
    public function phone(string $variableName, string $sourceFormat, string $urlEncoding=Mailcode_Factory::URL_ENCODING_NONE) : Mailcode_Commands_Command_ShowPhone
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $params = sprintf(
            '%s "%s"',
            $variableName,
            strtoupper($sourceFormat)
        );

        $cmd = $this->commands->createCommand(
            'ShowPhone',
            '',
            $params,
            sprintf(
                '{showphone: %s}',
                $params
            )
        );

        $this->instantiator->checkCommand($cmd);
        $this->instantiator->setEncoding($cmd, $urlEncoding);

        if($cmd instanceof Mailcode_Commands_Command_ShowPhone)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowPhone', $cmd);
    }

    public function snippet(string $snippetName) : Mailcode_Commands_Command_ShowSnippet
    {
        $snippetName = $this->instantiator->filterVariableName($snippetName);
        
        $cmd = $this->commands->createCommand(
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
