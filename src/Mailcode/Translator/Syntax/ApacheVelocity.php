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
    private $regexSpecialChars = array(
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
    public function filterRegexString(string $string) : string
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
        foreach($this->regexSpecialChars as $char)
        {
            $string = str_replace($char, '\\'.$char, $string);
        }
        
        // Restore the escaped quotes, which stay escaped 
        // with a single backslash.
        $string = str_replace('ESCQUOTE', '\\"', $string);

        return $string;
    }

    protected function addURLEncoding(Mailcode_Commands_Command $command, string $statement) : string
    {
        if($command->isURLEncoded())
        {
            return sprintf(
                '${esc.url($%s)}',
                $statement
            );
        }

        if($command->isURLDecoded())
        {
            return sprintf(
                '${esc.unurl($%s)}',
                $statement
            );
        }

        return sprintf(
            '${%s}',
            $statement
        );
    }

    public function renderNumberFormat(string $varName, Mailcode_Number_Info $numberInfo) : string
    {
        $varName = ltrim($varName, '$');

        return sprintf(
            "price.format(%s, %s, '%s', '%s')",
            '$'.$varName,
            $numberInfo->getDecimals(),
            $numberInfo->getDecimalsSeparator(),
            $numberInfo->getThousandsSeparator()
        );
    }

    public function renderStringToNumber(string $varName) : string
    {
        $varName = ltrim($varName, '$');

        return sprintf(
            '$price.toNumber(%s)',
            '$'.$varName
        );
    }
}
