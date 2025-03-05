<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Translator_Command_SetVariable;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_SetVariable} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class SetVariableTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_SetVariable
{
    public function translate(Mailcode_Commands_Command_SetVariable $command): string
    {
        $assignmentString = $command->getAssignmentString();

        if ($command->isCountEnabled())
        {
            $result = $this->buildCountAssignment($command);
            if($result !== null) {
                $assignmentString = $result;
            }
        }

        return sprintf(
            '{%% set %s = %s %%}',
            $this->formatVariableName($command->getVariable()->getFullName()),
            $this->formatVariablesInString($assignmentString)
        );
    }

    private function buildCountAssignment(Mailcode_Commands_Command_SetVariable $command) : ?string
    {
        $variable = $command->getCountVariable();

        if ($variable === null) {
            return null;
        }

        return sprintf(
            '%s|length',
            $this->formatVariableName($variable->getFullName())
        );
    }
}
