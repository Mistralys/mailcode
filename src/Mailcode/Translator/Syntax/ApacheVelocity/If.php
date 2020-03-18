<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_If} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_If
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "If" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_If extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_If
{
    public function translate(Mailcode_Commands_Command_If $command): string
    {
        if($command instanceof Mailcode_Commands_Command_If_Command)
        {
            return $this->translateCommand($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $this->translateVariable($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_If_Contains)
        {
            return $this->translateContains($command);
        }
        
        return '';
    }
    
    protected function translateCommand(Mailcode_Commands_Command_If_Command $command) : string
    {
        $params = $command->getParams();
        
        if(!$params)
        {
            return '';
        }
        
        return sprintf(
            '#if(%s)',
            $params->getNormalized()
        );
    }
    
    protected function translateVariable(Mailcode_Commands_Command_If_Variable $command) : string
    {
        return sprintf(
            '#if(%s %s %s)',
            $command->getVariable()->getFullName(),
            $command->getComparator(),
            $command->getValue()
        );
    }
    
    protected function translateContains(Mailcode_Commands_Command_If_Contains $command) : string
    {
        $opts = 's';
        if($command->isCaseInsensitive())
        {
            $opts = 'is';
        }
        
        return sprintf(
            '#if(%s.matches("(?%s)%s"))',
            $command->getVariable()->getFullName(),
            $opts,
            $this->filterRegexString(trim($command->getSearchTerm(), '"'))
        );
    }
}
