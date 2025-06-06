<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Number_Info;
use Mailcode\Mailcode_Number_LocalCurrency;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Translator\BaseCommandTranslation;
use function Mailcode\dollarize;

/**
 * Abstract base class for apache velocity command translation classes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class BaseApacheVelocityCommandTranslation extends BaseCommandTranslation
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

        return $this->renderEncodings($command, dollarize($varName));
    }

    public function renderNumberFormat(string $varName, Mailcode_Number_Info $numberInfo, bool $absolute): string
    {
        $varName = dollarize($varName);

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
        return sprintf(
            '$numeric.toNumber(%s)',
            dollarize($varName)
        );
    }

    public function renderPrice(string $varName, Mailcode_Number_LocalCurrency $localCurrency, bool $absolute = false, bool $withCurrencyName = true): string
    {
        $varName = dollarize($varName);

        if ($absolute) {
            $varName = sprintf('${numeric.abs(%s)}', $varName);
        }

        $numberInfo = $localCurrency->getFormatInfo();

        $currencyToken = $localCurrency->getCurrency();
        if ($currencyToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $displayedCurrency = $currencyToken->getNormalized();
        } else if ($currencyToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $displayedCurrency = '"' . $currencyToken->getText() . '"';
        } else if ($withCurrencyName) {
            $displayedCurrency = '"' . $localCurrency->getCurrencyName() . '"';
        } else {
            $displayedCurrency = '"' . $localCurrency->getCurrencySymbol() . '"';
        }

        $regionToken = $localCurrency->getRegion();
        if ($regionToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $displayedCountry = $regionToken->getNormalized();
        } else if ($regionToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $displayedCountry = '"' . $regionToken->getText() . '"';
        } else {
            $displayedCountry = '"' . $localCurrency->getCountry() . '"';
        }

        return sprintf(
            'money.amount(%s, "%s").group("%s").unit(%s, %s).separator("%s")',
            $varName,
            $numberInfo->getDecimalsSeparator(),
            $numberInfo->getThousandsSeparator(),
            $displayedCurrency,
            $displayedCountry,
            $localCurrency->getUnitSeparator()
        );
    }

    public function renderQuotedValue(string $value): string
    {
        return sprintf(
            '"%s"',
            str_replace('"', '\"', $value)
        );
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
            $result = $this->renderEncoding($encoding, $result, $command);
        }

        return $result;
    }

    protected function renderEncoding(string $keyword, string $result, EncodableInterface $command): string
    {
        $template = $this->encodingTemplates[$keyword] ?? '%s';

        return sprintf($template, $result);
    }
}
