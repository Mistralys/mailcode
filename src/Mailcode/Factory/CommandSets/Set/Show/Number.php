<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Number}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Number
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Factory_CommandSets_Set;

/**
 * Factory class for the `shownumber` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Number extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $variableName, string $formatString="", bool $absolute=false) : Mailcode_Commands_Command_ShowNumber
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        $paramsString = $this->compileNumberParams($formatString, $absolute);

        $cmd = $this->commands->createCommand(
            'ShowNumber',
            '',
            $variableName.$paramsString,
            sprintf(
                '{shownumber: %s%s}',
                $variableName,
                $paramsString
            )
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowNumber)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowNumber', $cmd);
    }

    private function compileNumberParams(string $formatString="", bool $absolute=false) : string
    {
        $params = array();

        if(!empty($formatString))
        {
            $params[] = sprintf(
                ' "%s"',
                $formatString
            );
        }

        if($absolute)
        {
            $params[] = ' absolute:';
        }

        if(!empty($params))
        {
            return ' '.implode(' ', $params);
        }

        return '';
    }
}
