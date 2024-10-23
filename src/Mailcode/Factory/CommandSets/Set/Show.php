<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_Show} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see Mailcode_Factory_CommandSets_Set_Show
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Factory\CommandSets\Set\Show\Date;
use Mailcode\Factory\CommandSets\Set\Show\Encoded;
use Mailcode\Factory\CommandSets\Set\Show\Number;
use Mailcode\Factory\CommandSets\Set\Show\Phone;
use Mailcode\Factory\CommandSets\Set\Show\Snippet;
use Mailcode\Factory\CommandSets\Set\Show\URL;

/**
 * Command set used to create showxxx commands.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_Show extends Mailcode_Factory_CommandSets_Set
{
    public function var(string $variableName): Mailcode_Commands_Command_ShowVariable
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $cmd = $this->commands->createCommand(
            'ShowVariable',
            '',
            $variableName,
            sprintf(
                '{showvar: %s}',
                $variableName
            )
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_ShowVariable) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowVariable', $cmd);
    }

    public function date(string $variableName, string $formatString = "", string $timezoneString = null, string $timezoneVariable = null): Mailcode_Commands_Command_ShowDate
    {
        return (new Date())->create($variableName, $formatString, $timezoneString, $timezoneVariable);
    }

    public function number(string $variableName, string $formatString = "", bool $absolute = false): Mailcode_Commands_Command_ShowNumber
    {
        return (new Number())->create($variableName, $formatString, $absolute);
    }

    /**
     * Creates a `showphone` command.
     *
     * @param string $variableName The name of the variable, with or without $ sign.
     * @param string $sourceFormat Two-letter country code, case-insensitive.
     * @param string $urlEncoding The URL encoding mode, if any.
     * @return Mailcode_Commands_Command_ShowPhone
     * @throws Mailcode_Factory_Exception
     */
    public function phone(string $variableName, string $sourceFormat, string $urlEncoding = Mailcode_Factory::URL_ENCODING_NONE): Mailcode_Commands_Command_ShowPhone
    {
        return (new Phone())->create($variableName, $sourceFormat, $urlEncoding);
    }

    /**
     * @param string $snippetName The name of the snippet to show.
     * @return Mailcode_Commands_Command_ShowSnippet
     * @throws Mailcode_Factory_Exception
     */
    public function snippet(string $snippetName, string $namespace = null): Mailcode_Commands_Command_ShowSnippet
    {
        return (new Snippet())->create($snippetName, $namespace);
    }

    /**
     * @param string $url The target URL. Can contain Mailcode.
     * @param string|null $trackingID If not set, an auto-generated tracking ID will be used.
     * @param array<string,string> $queryParams
     * @return Mailcode_Commands_Command_ShowURL
     * @throws Mailcode_Factory_Exception
     */
    public function url(string $url, ?string $trackingID = null, array $queryParams = array()): Mailcode_Commands_Command_ShowURL
    {
        return (new URL())->create($url, $trackingID, $queryParams);
    }

    /**
     * @param string $subject The string to encode
     * @param string[] $encodings The encodings (keywords) to enable
     * @return Mailcode_Commands_Command_ShowEncoded
     * @throws Mailcode_Factory_Exception
     */
    public function encoded(string $subject, array $encodings): Mailcode_Commands_Command_ShowEncoded
    {
        return (new Encoded())->create($subject, $encodings);
    }
}
