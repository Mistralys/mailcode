<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_If;
use Mailcode\Mailcode_Commands_Command_If_BeginsWith;
use Mailcode\Mailcode_Commands_Command_If_BiggerThan;
use Mailcode\Mailcode_Commands_Command_If_Command;
use Mailcode\Mailcode_Commands_Command_If_Contains;
use Mailcode\Mailcode_Commands_Command_If_Empty;
use Mailcode\Mailcode_Commands_Command_If_EndsWith;
use Mailcode\Mailcode_Commands_Command_If_EqualsNumber;
use Mailcode\Mailcode_Commands_Command_If_ListBeginsWith;
use Mailcode\Mailcode_Commands_Command_If_ListContains;
use Mailcode\Mailcode_Commands_Command_If_ListEndsWith;
use Mailcode\Mailcode_Commands_Command_If_ListEquals;
use Mailcode\Mailcode_Commands_Command_If_ListNotContains;
use Mailcode\Mailcode_Commands_Command_If_NotContains;
use Mailcode\Mailcode_Commands_Command_If_NotEmpty;
use Mailcode\Mailcode_Commands_Command_If_SmallerThan;
use Mailcode\Mailcode_Commands_Command_If_Variable;
use Mailcode\Mailcode_Translator_Command_If;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;
use Mailcode\Translator\Syntax\HubL\Base\AbstractIfBase;

/**
 * Translates the {@see Mailcode_Commands_Command_If} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class IfTranslation extends AbstractIfBase implements Mailcode_Translator_Command_If
{
    protected function getCommandTemplate() : string
    {
        return '{%% if %s %%}';
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
            $command->getValue(),
            $command->isCaseInsensitive()
        );
    }

    protected function translateContains(Mailcode_Commands_Command_If_Contains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            false,
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateNotContains(Mailcode_Commands_Command_If_NotContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            false,
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListContains(Mailcode_Commands_Command_If_ListContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->isRegexEnabled(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListEquals(Mailcode_Commands_Command_If_ListEquals $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            false,
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListNotContains(Mailcode_Commands_Command_If_ListNotContains $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->isRegexEnabled(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListBeginsWith(Mailcode_Commands_Command_If_ListBeginsWith $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->isRegexEnabled(),
            $command->getSearchTerms(),
            $command->getType()
        );
    }

    protected function translateListEndsWith(Mailcode_Commands_Command_If_ListEndsWith $command) : string
    {
        return $this->_translateContains(
            $command->getVariable(),
            $command->isCaseInsensitive(),
            $command->isRegexEnabled(),
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
