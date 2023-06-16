<?php
/**
 * File containing the class {@see Mailcode_Commands_Keywords}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Keywords
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Enum of all available keyword types that can be used
 * in commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Keywords
{
    public const TYPE_IN = 'in:';
    public const TYPE_INSENSITIVE = 'insensitive:';
    public const TYPE_REGEX = 'regex:';
    public const TYPE_URLENCODE = 'urlencode:';
    public const TYPE_URLDECODE = 'urldecode:';
    public const TYPE_MULTILINE = 'multiline:';
    public const TYPE_IDN_ENCODE = 'idnencode:';
    public const TYPE_IDN_DECODE = 'idndecode:';
    public const TYPE_NOHTML = 'nohtml:';
    public const TYPE_ABSOLUTE = 'absolute:';
    public const TYPE_NO_TRACKING = 'no-tracking:';
    public const TYPE_BREAK_AT = 'break-at:';
    public const TYPE_COUNT = 'count:';

    /**
     * @return string[]
     */
    public static function getAll(): array
    {
        return array(
            self::TYPE_IN,
            self::TYPE_INSENSITIVE,
            self::TYPE_REGEX,
            self::TYPE_URLENCODE,
            self::TYPE_URLDECODE,
            self::TYPE_MULTILINE,
            self::TYPE_IDN_ENCODE,
            self::TYPE_IDN_DECODE,
            self::TYPE_NOHTML,
            self::TYPE_ABSOLUTE,
            self::TYPE_NO_TRACKING,
            self::TYPE_BREAK_AT,
            self::TYPE_COUNT
        );
    }
}
