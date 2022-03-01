<?php
/**
 * File containing the class {@see \Mailcode\Parser\Statement\Tokenizer\SpecialChars}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Parser\Statement\Tokenizer\SpecialChars
 */

declare(strict_types=1);

namespace Mailcode\Parser\Statement\Tokenizer;

/**
 * Handles the encoding, decoding and escaping of
 * Mailcode special characters in a string.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class SpecialChars
{
    public const ESCAPE_CHAR = '\\';
    public const PLACEHOLDER_QUOTE = '__QUOTE__';
    public const PLACEHOLDER_BRACKET_OPEN = '__BRACKET_OPEN__';
    public const PLACEHOLDER_BRACKET_CLOSE = '__BRACKET_CLOSE__';

    private static array $charsEncoded = array(
        '"' => self::PLACEHOLDER_QUOTE,
        '{' => self::PLACEHOLDER_BRACKET_OPEN,
        '}' => self::PLACEHOLDER_BRACKET_CLOSE
    );

    private static array $charsEscaped = array(
        '"' => self::ESCAPE_CHAR.'"',
        '{' => self::ESCAPE_CHAR.'{',
        '}' => self::ESCAPE_CHAR.'}'
    );

    public static function getChars() : array
    {
        return self::$charsEncoded;
    }

    /**
     * Encodes a string for the internal string format,
     * which uses placeholders for special characters
     * like quotes and brackets.
     *
     * @param string $subject
     * @return string
     */
    public static function encodeAll(string $subject) : string
    {
        $subject = self::decode($subject);

        return str_replace(
            array_keys(self::$charsEncoded),
            array_values(self::$charsEncoded),
            $subject
        );
    }

    public static function encodeEscaped(string $subject) : string
    {
        return str_replace(
            array_values(self::$charsEscaped),
            array_values(self::$charsEncoded),
            $subject
        );
    }

    /**
     * Escapes all Mailcode special characters in the
     * specified string.
     *
     * @param string $subject
     * @return string
     */
    public static function escape(string $subject) : string
    {
        // Avoid double-encoding special characters
        $subject = self::decode($subject);

        return str_replace(
            array_keys(self::$charsEscaped),
            array_values(self::$charsEscaped),
            $subject
        );
    }

    /**
     * Decodes a string (including escaped characters) into
     * its raw form without escaped or encoded characters.
     *
     * @param string $subject
     * @return string
     */
    public static function decode(string $subject) : string
    {
        $replaces = array();

        foreach(self::$charsEncoded as $char => $placeholder)
        {
            $escaped = self::$charsEscaped[$char];

            $replaces[$escaped] = $char;
            $replaces[$placeholder] = $char;
        }

        return str_replace(
            array_keys($replaces),
            array_values($replaces),
            $subject
        );
    }
}
