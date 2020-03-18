<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_End} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_End
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "End" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_End extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_End
{
    public function translate(Mailcode_Commands_Command_End $command): string
    {
        return '#{end}';
    }
}
