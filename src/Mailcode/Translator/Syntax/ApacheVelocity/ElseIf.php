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
    
    protected function translateBeginsWith(Mailcode_Commands_Command_ElseIf_BeginsWith $command) : string
    {
        return $this->_translateSearch(
            'starts',
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerm()
        );
    }
    
    protected function translateEndsWith(Mailcode_Commands_Command_ElseIf_EndsWith $command) : string
    {
        return $this->_translateSearch(
            'ends',
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerm()
        );
    }

    protected function translateBiggerThan(Mailcode_Commands_Command_ElseIf_BiggerThan $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '>'
        );
    }

    protected function translateSmallerThan(Mailcode_Commands_Command_ElseIf_SmallerThan $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '<'
        );
    }

    protected function translateEqualsNumber(Mailcode_Commands_Command_ElseIf_EqualsNumber $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '=='
        );
    }

    protected function translateCommand(Mailcode_Commands_Command_ElseIf_Command $command) : string
    {
        return $this->_translateGeneric($command);
    }
    
    protected function translateVariable(Mailcode_Commands_Command_ElseIf_Variable $command) : string
    {
        return $this->_translateVariable(
            $command->getVariable(),
            $command->getSign(),
            $command->getValue()
        );
    }
    
    protected function translateContains(Mailcode_Commands_Command_ElseIf_Contains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms()
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
