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

use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
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
    public function create(string $variableName, string $formatString = "", string $timezoneString = null, string $timezoneVariable = null): Mailcode_Commands_Command_ShowDate
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $format = $this->getFormat($formatString);
        $timezone = $this->getTimezone($timezoneString, $timezoneVariable);

        $cmd = $this->commands->createCommand(
            'ShowDate',
            '',
            $variableName . $format . $timezone,
            sprintf(
                '{showdate: %s%s%s}',
                $variableName,
                $format,
                $timezone
            )
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_ShowDate) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowDate', $cmd);
    }

    public function now(string $formatString = "", string $timezoneString = null, string $timezoneVariable = null): Mailcode_Commands_Command_ShowDate
    {
        $format = $this->getFormat($formatString);
        $timezone = $this->getTimezone($timezoneString, $timezoneVariable);

        $cmd = $this->commands->createCommand(
            'ShowDate',
            '',
            $format . $timezone,
            sprintf(
                '{showdate: %s%s}',
                $format,
                $timezone
            )
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_ShowDate) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowDate', $cmd);
    }

    /**
     * @param string $formatString
     * @return string
     */
    private function getFormat(string $formatString): string
    {
        $format = '';
        if (!empty($formatString)) {
            $format = sprintf(
                ' "%s"',
                $formatString
            );
        }
        return $format;
    }

    /**
     * @param string|null $timezoneString
     * @param string|null $timezoneVariable
     * @return string
     */
    private function getTimezone(?string $timezoneString, ?string $timezoneVariable): string
    {
        $timezone = '';
        if (!empty($timezoneString)) {
            $timezone = sprintf(
                ' ' . TimezoneInterface::PARAMETER_NAME . '=%s',
                $this->quoteString($timezoneString)
            );
        } else if (!empty($timezoneVariable)) {
            $timezone = sprintf(
                ' ' . TimezoneInterface::PARAMETER_NAME . '=%s',
                $timezoneVariable
            );
        }
        return $timezone;
    }
}
