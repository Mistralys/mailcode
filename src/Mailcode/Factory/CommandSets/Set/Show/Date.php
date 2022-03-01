<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Date}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Date
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Factory_CommandSets_Set;

/**
 * Factory class for the `showdate` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Date extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $variableName, string $formatString="") : Mailcode_Commands_Command_ShowDate
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $format = '';
        if(!empty($formatString))
        {
            $format = sprintf(
                ' "%s"',
                $formatString
            );
        }

        $cmd = $this->commands->createCommand(
            'ShowDate',
            '',
            $variableName.$format,
            sprintf(
                '{showdate: %s%s}',
                $variableName,
                $format
            )
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowDate)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowDate', $cmd);
    }
}