<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_Break} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_Break
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "Break" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_Break extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_Break
{
    public function translate(Mailcode_Commands_Command_Break $command): string
    {
        return '#{break}';
    }
}
