<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\EncodableInterface;

/**
 * Abstract base class for apache velocity command translation classes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Translator_Syntax_ApacheVelocity extends Mailcode_Translator_Command
{
    /**
     * @var string[]
     */
    private array $regexSpecialChars = array(
        '?',
        '.',
        '[',
        ']',
        '|',
        '{',
        '}',
        '$',
        '*',
        '^',
        '+',
        '<',
        '>',
        '(',
        ')'
    );

    public function getLabel(): string
    {
        return 'Apache Velocity';
    }

    /**
     * Filters the string for use in an Apache Velocity (Java)
     * regex string: escapes all special characters.
     *
     * Velocity does its own escaping, so no need to escape special
     * characters as if they were a javascript string.
     *
     * @param string $string
     * @return string
     */
    public function filterRegexString(string $string): string
    {
        // Special case: previously escaped quotes.
        // To avoid modifying them, we strip them out.
        $string = str_replace('\\"', 'ESCQUOTE', $string);

        // Any other existing backslashes in the string
        // have to be double-escaped, giving four
        // backslashes in the java regex.
        $string = str_replace('\\', '\\\\', $string);

        // All other special characters have to be escaped
        // with two backslashes.
        foreach ($this->regexSpecialChars as $char) {
            $string = str_replace($char, '\\' . $char, $string);
        }

        // Restore the escaped quotes, which stay escaped
        // with a single backslash.
        $string = str_replace('ESCQUOTE', '\\"', $string);

        return $string;
    }

    protected function renderVariableEncodings(Mailcode_Commands_Command $command, string $varName): string
    {
        if (!$command instanceof EncodableInterface || !$command->hasActiveEncodings()) {
            return sprintf(
                '${%s}',
                $varName
            );
        }

        return $this->renderEncodings($command, '$' . $varName);
    }

    public function renderNumberFormat(string $varName, Mailcode_Number_Info $numberInfo, bool $absolute): string
    {
        $varName = '$' . ltrim($varName, '$');

        if ($absolute) {
            $varName = sprintf('${numeric.abs(%s)}', $varName);
        }

        return sprintf(
            "numeric.format(%s, %s, '%s', '%s')",
            $varName,
            $numberInfo->getDecimals(),
            $numberInfo->getDecimalsSeparator(),
            $numberInfo->getThousandsSeparator()
        );
    }

    public function renderStringToNumber(string $varName): string
    {
        $varName = ltrim($varName, '$');

        return sprintf(
            '$numeric.toNumber(%s)',
            '$' . $varName
        );
    }

    public function renderQuotedValue(string $value): string
    {
        return sprintf(
            '"%s"',
            str_replace('"', '\"', $value)
        );
    }

    public function getSyntaxName(): string
    {
        return 'ApacheVelocity';
    }

    /**
     * @var array<string,string>
     */
    private array $encodingTemplates = array(
        Mailcode_Commands_Keywords::TYPE_URLENCODE => '${esc.url(%s)}',
        Mailcode_Commands_Keywords::TYPE_URLDECODE => '${esc.unurl(%s)}',
        Mailcode_Commands_Keywords::TYPE_IDN_ENCODE => '${text.idn(%s)}',
        Mailcode_Commands_Keywords::TYPE_IDN_DECODE => '${text.unidn(%s)}'
    );

    protected function renderEncodings(EncodableInterface $command, string $statement): string
    {
        $encodings = $command->getActiveEncodings();
        $result = $statement;

        foreach ($encodings as $encoding) {
            $result = $this->renderEncoding($encoding, $result);
        }

        return $result;
    }

    protected function renderEncoding(string $keyword, string $result): string
    {
        $template = $this->encodingTemplates[$keyword] ?? '%s';

        return sprintf($template, $result);
    }
}
