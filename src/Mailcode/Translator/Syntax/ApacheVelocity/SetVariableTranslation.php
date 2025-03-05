<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator_Command_SetVariable;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;
use function Mailcode\dollarize;

/**
 * Translates the {@see Mailcode_Commands_Command_SetVariable} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class SetVariableTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_SetVariable
{
    /**
     * @param Mailcode_Commands_Command_SetVariable $command
     * @return string
     * @throws Mailcode_Exception
     */
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
            '#set(%s = %s)',
            $command->getVariable()->getFullName(),
            $assignmentString
        );
    }

    private function buildCountAssignment(Mailcode_Commands_Command_SetVariable $command) : ?string
    {
        $variable = $command->getCountVariable();

        if ($variable === null) {
            return null;
        }

        if($variable->hasPath()) {
            return sprintf(
                '$map.of(%s).keys("%s").count()',
                dollarize($variable->getPath()),
                $variable->getName()
            );
        }

        return sprintf(
            '$map.of(%s).count()',
            dollarize($variable->getName())
        );
    }
}
