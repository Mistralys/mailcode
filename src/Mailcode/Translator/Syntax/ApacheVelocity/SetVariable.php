<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_SetVariable} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_SetVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "SetVariable" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_SetVariable extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_SetVariable
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
