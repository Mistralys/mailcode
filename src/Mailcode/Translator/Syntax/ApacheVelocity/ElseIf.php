<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ElseIf} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ElseIf
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ElseIf" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ElseIf extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ElseIf
{
    public function translate(Mailcode_Commands_Command_ElseIf $command): string
    {
        if($command instanceof Mailcode_Commands_Command_ElseIf_Command)
        {
            return $this->translateCommand($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $this->translateVariable($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Contains)
        {
            return $this->translateContains($command);
        }
        
        return '';
    }
    
    protected function translateCommand(Mailcode_Commands_Command_ElseIf_Command $command) : string
    {
        $params = $command->getParams();
        
        if(!$params)
        {
            return '';
        }
        
        return sprintf(
            '#elseif(%s)',
            $params->getNormalized()
        );
    }
    
    protected function translateVariable(Mailcode_Commands_Command_ElseIf_Variable $command) : string
    {
        return sprintf(
            '#elseif(%s %s %s)',
            $command->getVariable()->getFullName(),
            $command->getComparator(),
            $command->getValue()
        );
    }
    
    protected function translateContains(Mailcode_Commands_Command_ElseIf_Contains $command) : string
    {
        $opts = 's';
        if($command->isCaseInsensitive())
        {
            $opts = 'is';
        }
        
        return sprintf(
            '#elseif(%s.matches("(?%s)%s"))',
            $command->getVariable()->getFullName(),
            $opts,
            $this->filterRegexString(trim($command->getSearchTerm(), '"'))
        );
    }
}
