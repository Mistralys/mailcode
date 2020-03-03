<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Allows translation mailcode to apache velocity syntax.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity extends Mailcode_Translator_Syntax
{
    protected function _translateElseIf(Mailcode_Commands_Command_ElseIf $command): string
    {
        $params = $command->getParams();
        
        if($params)
        {
            return sprintf(
                '#elseif(%s)',
                $params->getNormalized()
            );
        }
        
        return '';
    }

    protected function _translateElse(Mailcode_Commands_Command_Else $command): string
    {
        return '#{else}';
    }

    protected function _translateIf(Mailcode_Commands_Command_If $command): string
    {
        $params = $command->getParams();
    
        if($params)
        {
            return sprintf(
                '#if(%s)',
                $params->getNormalized()
            );
        }
        
        return '';
    }

    protected function _translateShowVariable(Mailcode_Commands_Command_ShowVariable $command): string
    {
        return '${'.ltrim($command->getVariableName(), '$').'}';
    }

    protected function _translateFor(Mailcode_Commands_Command_For $command): string
    {
        $params = $command->getParams();
        
        if($params)
        {
            return sprintf(
                '#for(%s)',
                $params->getNormalized()
            );
        }
        
        return '';
    }

    protected function _translateSetVariable(Mailcode_Commands_Command_SetVariable $command): string
    {
        $params = $command->getParams();
        
        if($params)
        {
            return sprintf(
                '#set(%s)',
                $params->getNormalized()
            );
        }
        
        return '';
    }

    protected function _translateEnd(Mailcode_Commands_Command_End $command): string
    {
        return '#{end}';
    }
}
