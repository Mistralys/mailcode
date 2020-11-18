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
    const TYPE_IN = 'in:';
    const TYPE_INSENSITIVE = 'insensitive:';
    const TYPE_URLENCODE = 'urlencode:';
    const TYPE_URLDECODE = 'urldecode:';

    /**
     * @return string[]
     */
    public static function getAll() : array
    {
        return array(
            self::TYPE_IN,
            self::TYPE_INSENSITIVE,
            self::TYPE_URLDECODE,
            self::TYPE_URLENCODE
        );
    }
}
