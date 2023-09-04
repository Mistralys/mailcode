<?php
/**
 * File containing the {@see Mailcode_Date_FormatInfo} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Date_FormatInfo
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\OperationResult;

/**
 * Main hub for information all around the date format strings
 * that can be used in the ShowDate command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Date_FormatInfo
{
    public const VALIDATION_INVALID_FORMAT_CHARACTER = 55801;
    public const VALIDATION_EMPTY_FORMAT_STRING = 55802;

    public const CHARTYPE_DATE = 'date';
    public const CHARTYPE_TIME = 'time';
    public const CHARTYPE_PUNCTUATION = 'punctuation';

    public const CHAR_DAY_LZ = 'd';
    public const CHAR_DAY_NZ = 'j';
    public const CHAR_MONTH_LZ = 'm';
    public const CHAR_MONTH_NZ = 'n';
    public const CHAR_YEAR_4 = 'Y';
    public const CHAR_YEAR_2 = 'y';
    public const CHAR_HOUR_24_LZ = 'H';
    public const CHAR_HOUR_24_NZ = 'G';
    public const CHAR_HOUR_12_LZ = 'h';
    public const CHAR_HOUR_12_NZ = 'g';
    public const CHAR_AM_PM = 'a';
    public const CHAR_MINUTES_LZ = 'i';
    public const CHAR_SECONDS_LZ = 's';
    public const CHAR_MILLISECONDS = 'v';
    public const CHAR_TIMEZONE = 'e';

    /**
     * @var string
     */
    private string $defaultFormat = "Y/m/d";

    /**
     * @var Mailcode_Date_FormatInfo_Character[]
     */
    private array $formatChars = array();

    /**
     * @var string[]
     */
    private array $allowedChars = array();

    /**
     * @var Mailcode_Date_FormatInfo|NULL
     */
    private static ?Mailcode_Date_FormatInfo $instance = null;

    private function __construct()
    {
        $this->initCharacters();
    }

    public static function getInstance(): Mailcode_Date_FormatInfo
    {
        if (!isset(self::$instance)) {
            self::$instance = new Mailcode_Date_FormatInfo();
        }

        return self::$instance;
    }

    /**
     * Initialized the list of allowed date formatting
     * characters. This is done only once per request
     * by storing them statically for performance reasons.
     */
    private function initCharacters(): void
    {
        $chars = array(
            array(self::CHARTYPE_DATE, self::CHAR_DAY_LZ, t('Day of the month, with leading zeros')),
            array(self::CHARTYPE_DATE, self::CHAR_DAY_NZ, t('Day of the month, without leading zeros')),
            array(self::CHARTYPE_DATE, self::CHAR_MONTH_LZ, t('Month number, with leading zeros')),
            array(self::CHARTYPE_DATE, self::CHAR_MONTH_NZ, t('Month number, without leading zeros')),
            array(self::CHARTYPE_DATE, self::CHAR_YEAR_4, t('Year, 4 digits')),
            array(self::CHARTYPE_DATE, self::CHAR_YEAR_2, t('Year, 2 digits')),

            array(self::CHARTYPE_TIME, self::CHAR_HOUR_24_LZ, t('Hour, 24-hour format with leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_HOUR_24_NZ, t('Hour, 24-hour format without leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_HOUR_12_LZ, t('Hour, 12-hour format with leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_HOUR_12_NZ, t('Hour, 12-hour format without leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_AM_PM, t('AM/PM marker')),
            array(self::CHARTYPE_TIME, self::CHAR_MINUTES_LZ, t('Minutes with leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_SECONDS_LZ, t('Seconds with leading zeros')),
            array(self::CHARTYPE_TIME, self::CHAR_MILLISECONDS, t('Milliseconds')),
            array(self::CHARTYPE_TIME, self::CHAR_TIMEZONE, t('Timezone')),

            array(self::CHARTYPE_PUNCTUATION, '.', t('Dot')),
            array(self::CHARTYPE_PUNCTUATION, '/', t('Slash')),
            array(self::CHARTYPE_PUNCTUATION, '-', t('Hyphen')),
            array(self::CHARTYPE_PUNCTUATION, ':', t('Colon')),
            array(self::CHARTYPE_PUNCTUATION, ' ', t('Space'))
        );

        foreach ($chars as $def) {
            $char = new Mailcode_Date_FormatInfo_Character(
                $def[0],
                $def[1],
                $def[2]
            );

            $this->formatChars[] = $char;
            $this->allowedChars[] = $char->getChar();
        }
    }

    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }

    public function setDefaultFormat(string $formatString): void
    {
        $this->defaultFormat = $formatString;
    }

    /**
     * Validates the date format string, by ensuring that
     * all the characters it is composed of are known.
     *
     * @param string $formatString
     * @return OperationResult
     *
     * @see Mailcode_Commands_Command_ShowDate::VALIDATION_EMPTY_FORMAT_STRING
     * @see Mailcode_Commands_Command_ShowDate::VALIDATION_INVALID_FORMAT_CHARACTER
     */
    public function validateFormat(string $formatString): OperationResult
    {
        $result = new OperationResult($this);

        $trimmed = trim($formatString);

        if (empty($trimmed)) {
            $result->makeError(
                t('Empty date format.'),
                self::VALIDATION_EMPTY_FORMAT_STRING
            );

            return $result;
        }

        $chars = ConvertHelper::string2array($formatString);
        $total = count($chars);

        for ($i = 0; $i < $total; $i++) {
            $char = $chars[$i];

            if (!in_array($char, $this->allowedChars)) {
                $result->makeError(
                    t('Invalid character in date format:') . ' ' .
                    t('%1$s at position %2$s.', '<code>' . $char . '</code>', $i + 1),
                    self::VALIDATION_INVALID_FORMAT_CHARACTER
                );

                return $result;
            }
        }

        return $result;
    }

    /**
     * Retrieves all characters that are allowed to
     * be used in a date format string, with information
     * on each.
     *
     * @return Mailcode_Date_FormatInfo_Character[]
     */
    public function getCharactersList(): array
    {
        return $this->formatChars;
    }

    /**
     * Retrieves the characters list, grouped by type label.
     *
     * @return array<string,array<int,Mailcode_Date_FormatInfo_Character>>
     */
    public function getCharactersGrouped(): array
    {
        $grouped = array();

        foreach ($this->formatChars as $char) {
            $type = $char->getTypeLabel();

            if (!isset($grouped[$type])) {
                $grouped[$type] = array();
            }

            $grouped[$type][] = $char;
        }

        $groups = array_keys($grouped);

        foreach ($groups as $group) {
            usort($grouped[$group], function (Mailcode_Date_FormatInfo_Character $a, Mailcode_Date_FormatInfo_Character $b) {
                return strnatcasecmp($a->getChar(), $b->getChar());
            });
        }

        uksort($grouped, function (string $a, string $b) {
            return strnatcasecmp($a, $b);
        });

        return $grouped;
    }
}
