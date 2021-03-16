<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_Mono} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_Mono
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "Mono" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Commands_Command_Mono
 */
class Mailcode_Translator_Syntax_ApacheVelocity_Mono extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_Mono
{
    public function translate(Mailcode_Commands_Command_Mono $command): string
    {
        // We do not want the command to be shown in the resulting
        // translated text. The preprocessor must be used if these
        // commands are to be transformed in the document.
        return '';
    }
}
