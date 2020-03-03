<?php
/**
 * File containing the {@see Mailcode_Translator} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to translate mailcode syntax to other syntaxes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator
{
    public function createSyntax(string $syntaxID) : Mailcode_Translator_Syntax
    {
        $class = '\Mailcode\Mailcode_Translator_Syntax_'.$syntaxID;
        
        return new $class();
    }
}
