<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use AppUtils\ConvertHelper;
use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Date_FormatInfo;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Translator_Command_ShowDate;
use Mailcode\Mailcode_Translator_Exception;
use Mailcode\Translator\Syntax\ApacheVelocity;
use function Mailcode\undollarize;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowDate} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowDateTranslation extends ApacheVelocity implements Mailcode_Translator_Command_ShowDate
{
    public const ERROR_UNKNOWN_DATE_FORMAT_CHARACTER = 55501;
    public const ERROR_UNHANDLED_TIME_ZONE_TOKEN_TYPE = 55502;

    /**
     * The date format used in the date variable. This is used to convert
     * the native date to the format specified in the variable command.
     */
    public const DEFAULT_INTERNAL_FORMAT = "yyyy-MM-dd'T'HH:mm:ss.SSSXXX";

    /**
     * Conversion table for the date format characters, from PHP to Java.
     *
     * @var array<string,string>
     * @see https://stackoverflow.com/questions/12781273/what-are-the-date-formats-available-in-simpledateformat-class
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    public static array $charTable = array(
        Mailcode_Date_FormatInfo::CHAR_DAY_LZ => 'dd', // Day of the month with leading zeros
        Mailcode_Date_FormatInfo::CHAR_DAY_NZ => 'd', // Day of the month without leading zeros
        Mailcode_Date_FormatInfo::CHAR_MONTH_LZ => 'MM', // Month number with leading zeros
        Mailcode_Date_FormatInfo::CHAR_MONTH_NZ => 'M', // Month number without leading zeros
        Mailcode_Date_FormatInfo::CHAR_YEAR_4 => 'yyyy', // Year with four digits
        Mailcode_Date_FormatInfo::CHAR_YEAR_2 => 'yy', // Year with two digits
        Mailcode_Date_FormatInfo::CHAR_HOUR_24_LZ => 'HH', // Hour in 24-hour format with leading zeros
        Mailcode_Date_FormatInfo::CHAR_HOUR_24_NZ => 'H', // Hour in 24-hour format without leading zeros
        Mailcode_Date_FormatInfo::CHAR_HOUR_12_LZ => 'hh', // 12-hour hour with leading zeros
        Mailcode_Date_FormatInfo::CHAR_HOUR_12_NZ => 'h', // 12-hour hour without leading zeros
        Mailcode_Date_FormatInfo::CHAR_AM_PM => 'a', // am/pm marker (lowercase in Java)
        Mailcode_Date_FormatInfo::CHAR_MINUTES_LZ => 'mm', // Minutes with leading zeros
        Mailcode_Date_FormatInfo::CHAR_SECONDS_LZ => 'ss', // Seconds with leading zeros
        Mailcode_Date_FormatInfo::CHAR_MILLISECONDS => 'SSS', // Milliseconds
        Mailcode_Date_FormatInfo::CHAR_TIMEZONE => 'XXX', // Timezone identifier, e.g. "UTC", "GMT" or "Europe/Paris"

        // PUNCTUATION
        '.' => '.',
        ':' => ':',
        '-' => '-',
        '/' => '/',
        ' ' => ' ',
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
        $statement = sprintf(
            'time.input("%s", $%s).output("%s")%s',
            $this->getInternalFormat($command),
            undollarize($command->getVariableName()),
            $this->resolveJavaFormat($command->getFormatString()),
            $this->resolveTimeZoneFormat($command)
        );

        return $this->renderVariableEncodings($command, $statement);
    }

    private function resolveTimeZoneFormat(Mailcode_Commands_Command_ShowDate $command): string
    {
        $token = $command->getTimezoneToken();

        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            return sprintf('.zone("%s")', $token->getText());
        }

        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            return sprintf('.zone(%s)', $token->getVariable()->getFullName());
        }

        throw new Mailcode_Translator_Exception(
            'Unknown time zone type.',
            sprintf(
                'The time zone token type is unhandled: [%s].',
                get_class($token)
            ),
            self::ERROR_UNHANDLED_TIME_ZONE_TOKEN_TYPE
        );
    }

    /**
     * @param string $formatString
     * @return string
     * @throws Mailcode_Translator_Exception {@see self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER}
     */
    private function resolveJavaFormat(string $formatString): string
    {
        $chars = ConvertHelper::string2array($formatString);
        $result = array();

        foreach ($chars as $char) {
            if (!isset(self::$charTable[$char])) {
                throw new Mailcode_Translator_Exception(
                    'Unknown date format string character',
                    sprintf(
                        'No translation for character %s available.',
                        ConvertHelper::hidden2visible($char)
                    ),
                    self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER
                );
            }

            $result[] = self::$charTable[$char];
        }

        return implode('', $result);
    }
}
