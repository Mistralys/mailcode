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
class Mailcode_Translator_Syntax_ApacheVelocity_If extends Mailcode_Translator_Syntax_ApacheVelocity_Base_AbstractIf implements Mailcode_Translator_Command_If
{
    protected function getCommandTemplate() : string
    {
        return '#if(%s)';
    }
    
    public function translate(Mailcode_Commands_Command_If $command): string
    {
        return $this->_translate($command);
    }
    
    protected function translateBody(Mailcode_Commands_IfBase $command) : string
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
        
        if($command instanceof Mailcode_Commands_Command_If_Empty)
        {
            return $this->translateEmpty($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_If_NotEmpty)
        {
            return $this->translateNotEmpty($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_If_BeginsWith)
        {
            return $this->translateBeginsWith($command);
        }
        
        if($command instanceof Mailcode_Commands_Command_If_EndsWith)
        {
            return $this->translateEndsWith($command);
        }
        
        return '';
    }

    protected function translateCommand(Mailcode_Commands_Command_If_Command $command) : string
    {
        return $this->_translateGeneric($command);
    }
    
    protected function translateBeginsWith(Mailcode_Commands_Command_If_BeginsWith $command) : string
    {
        return $this->_translateSearch(
            'starts',
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerm()
        );
    }
    
    protected function translateEndsWith(Mailcode_Commands_Command_If_EndsWith $command) : string
    {
        return $this->_translateSearch(
            'ends',
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerm()
        );
    }
    
    protected function translateVariable(Mailcode_Commands_Command_If_Variable $command) : string
    {
        return $this->_translateVariable(
            $command->getVariable(), 
            $command->getSign(), 
            $command->getValue()
        );
    }
    
    protected function translateContains(Mailcode_Commands_Command_If_Contains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(), 
            $command->isCaseInsensitive(), 
            $command->getSearchTerms()
        );
    }
    
    protected function translateEmpty(Mailcode_Commands_Command_If_Empty $command) : string
    {
        return $this->_translateEmpty($command->getVariable(), false);
    }
    
    protected function translateNotEmpty(Mailcode_Commands_Command_If_NotEmpty $command) : string
    {
        return $this->_translateEmpty($command->getVariable(), true);
    }
}
