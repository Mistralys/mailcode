<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_Else} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_Else
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "Else" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_Else extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_Else
{
    public function translate(Mailcode_Commands_Command_Else $command): string
    {
        return '#{else}';
    }
}
