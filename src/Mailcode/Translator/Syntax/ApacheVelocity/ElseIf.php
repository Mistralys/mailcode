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
class Mailcode_Translator_Syntax_ApacheVelocity_ElseIf extends Mailcode_Translator_Syntax_ApacheVelocity_Base_AbstractIf implements Mailcode_Translator_Command_ElseIf
{
    protected function getCommandTemplate() : string
    {
        return '#elseif(%s)';
    }
    
    public function translate(Mailcode_Commands_Command_ElseIf $command): string
    {
        return $this->_translate($command);
    }
    
    protected function translateBody(Mailcode_Commands_IfBase $command) : string
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
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Empty)
        {
            return $this->translateEmpty($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_NotEmpty)
        {
            return $this->translateNotEmpty($command);
        }
        
        return '';
    }
    
    protected function translateCommand(Mailcode_Commands_Command_ElseIf_Command $command) : string
    {
        return $this->_translateGeneric($command);
    }
    
    protected function translateVariable(Mailcode_Commands_Command_ElseIf_Variable $command) : string
    {
        return $this->_translateVariable(
            $command->getVariable(),
            $command->getComparator(),
            $command->getValue()
        );
    }
    
    protected function translateContains(Mailcode_Commands_Command_ElseIf_Contains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerm()
        );
    }
    
    protected function translateEmpty(Mailcode_Commands_Command_ElseIf_Empty $command) : string
    {
        return $this->_translateEmpty($command->getVariable(), false);
    }

    protected function translateNotEmpty(Mailcode_Commands_Command_ElseIf_NotEmpty $command) : string
    {
        return $this->_translateEmpty($command->getVariable(), true);
    }
}
