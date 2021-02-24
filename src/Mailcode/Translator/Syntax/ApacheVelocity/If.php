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

    protected function translateBiggerThan(Mailcode_Commands_Command_If_BiggerThan $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '>'
        );
    }

    protected function translateSmallerThan(Mailcode_Commands_Command_If_SmallerThan $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '<'
        );
    }

    protected function translateEqualsNumber(Mailcode_Commands_Command_If_EqualsNumber $command) : string
    {
        return $this->_translateNumberComparison(
            $command->getVariable(),
            $command->getNumber(),
            '=='
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
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateNotContains(Mailcode_Commands_Command_If_NotContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListContains(Mailcode_Commands_Command_If_ListContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListNotContains(Mailcode_Commands_Command_If_ListNotContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListBeginsWith(Mailcode_Commands_Command_If_ListBeginsWith $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListEndsWith(Mailcode_Commands_Command_If_ListEndsWith $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->getSearchTerms(),
            $command->getType()
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
