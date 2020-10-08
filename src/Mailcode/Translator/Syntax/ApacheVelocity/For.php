<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_For} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_For
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "For" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_For extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_For
{
    public function translate(Mailcode_Commands_Command_For $command): string
    {
        return sprintf(
            '#{foreach}(%s in %s)',
            $command->getLoopVariable()->getFullName(),
            $command->getSourceVariable()->getFullName()
        );
    }
}
