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
        $loopBreak = '';
        if ($command->isBreakAtEnabled()) {
            $token = $command->getBreakAtToken();
            if ($token != null) {
                $loopBreak = sprintf(' #if($foreach.count > %s) #break #end', $token->getMatchedText());
            }
        }

        // Using $source.list() here to ensure that Velocity always treats
        // the variable as a list, even if there is only a single entry
        // in the list (it would otherwise iterate over the keys in the
        // single entry).
        return sprintf(
            '#{foreach}(%s in %s.list())%s',
            $command->getLoopVariable()->getFullName(),
            $command->getSourceVariable()->getFullName(),
            $loopBreak
        );
    }
}
