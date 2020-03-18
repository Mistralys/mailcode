<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ShowSnippet" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowSnippet
{
    public function translate(Mailcode_Commands_Command_ShowSnippet $command): string
    {
        return sprintf(
            '${%s}',
            $command->getVariable()->getFullName()
        );
    }
}
