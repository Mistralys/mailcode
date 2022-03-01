<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Phone}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Phone
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowPhone;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_CommandSets_Set;
use Mailcode\Mailcode_Factory_Exception;

/**
 * Factory class for the `showphone` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Phone extends Mailcode_Factory_CommandSets_Set
{
    /**
     * Creates a `showphone` command.
     *
     * @param string $variableName The name of the variable, with or without $ sign.
     * @param string $sourceFormat Two-letter country code, case-insensitive.
     * @param string $urlEncoding The URL encoding mode, if any.
     * @return Mailcode_Commands_Command_ShowPhone
     * @throws Mailcode_Factory_Exception
     */
    public function create(string $variableName, string $sourceFormat, string $urlEncoding=Mailcode_Factory::URL_ENCODING_NONE) : Mailcode_Commands_Command_ShowPhone
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $params = sprintf(
            '%s "%s"',
            $variableName,
            strtoupper($sourceFormat)
        );

        $cmd = $this->commands->createCommand(
            'ShowPhone',
            '',
            $params,
            sprintf(
                '{showphone: %s}',
                $params
            )
        );

        $this->instantiator->checkCommand($cmd);
        $this->instantiator->setEncoding($cmd, $urlEncoding);

        if($cmd instanceof Mailcode_Commands_Command_ShowPhone)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowPhone', $cmd);
    }
}
