<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowDate} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowDate
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Translates the "ShowDate" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowDate extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowDate
{
    public const ERROR_UNKNOWN_DATE_FORMAT_CHARACTER = 55501;

    /**
     * The date format used in the date variable. This is used to convert
     * the native date to the format specified in the variable command.
     */
    public const DEFAULT_INTERNAL_FORMAT = "yyyy-MM-dd'T'HH:mm:ss.SSSXXX";

    /**
     * @var string[]string
     */
    private $charTable = array(
        'd' => 'dd',
        'j' => 'd',
        'm' => 'MM',
        'n' => 'M',
        'Y' => 'yyyy',
        'y' => 'yy',
        'H' => 'H',
        'i' => 'm',
        's' => 's',
        '.' => '.',
        ':' => ':',
        '-' => '-',
        '/' => '/',
        ' ' => ' '
    );

    public function getInternalFormat(Mailcode_Commands_Command_ShowDate $command): string
    {
        $internalFormat = $command->getTranslationParam('internal_format');

        if (is_string($internalFormat) && !empty($internalFormat)) {
            return $internalFormat;
        }

        return self::DEFAULT_INTERNAL_FORMAT;
    }

    public function translate(Mailcode_Commands_Command_ShowDate $command): string
    {
        $internalFormat = $this->getInternalFormat($command);
        $varName = ltrim($command->getVariableName(), '$');
        $javaFormat = $this->translateFormat($command->getFormatString());

        $timezoneFormat = sprintf('.zone("%s")', $command->getTimezone() ?? 'UTC');

        $statement = sprintf(
            'time.input("%s", $%s).output("%s")%s',
            $internalFormat,
            $varName,
            $javaFormat,
            $timezoneFormat
        );

        return $this->renderVariableEncodings($command, $statement);
    }

    private function translateFormat(string $formatString): string
    {
        $chars = ConvertHelper::string2array($formatString);
        $result = array();

        foreach ($chars as $char) {
            if (!isset($this->charTable[$char])) {
                throw new Mailcode_Translator_Exception(
                    'Unknown date format string character',
                    sprintf(
                        'No translation for character %s available.',
                        ConvertHelper::hidden2visible($char)
                    ),
                    self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER
                );
            }

            $result[] = $this->charTable[$char];
        }

        return implode('', $result);
    }
}
