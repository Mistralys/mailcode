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
    * Filters the string for use in an Apache Velocity (Java)
    * regex string: escapes all special characters.
    * 
    * @param string $string
    * @return string
    */
    protected function filterRegexString(string $string) : string
    {
        $escape = array(
            '\\',
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
        
        foreach($escape as $char)
        {
            $string = str_replace($char, '\\'.$char, $string);
        }
        
        return $string;
    }
}
