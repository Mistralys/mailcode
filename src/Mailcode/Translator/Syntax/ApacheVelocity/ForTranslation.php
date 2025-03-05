<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Translator_Command_For;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_For} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ForTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_For
{
    public function translate(Mailcode_Commands_Command_For $command): string
    {
        $loopBreak = '';
        if ($command->isBreakAtEnabled()) {
            $token = $command->getBreakAtToken();
            if ($token !== null) {
                $loopBreak = sprintf('#if($foreach.count > %s)#{break}#{end}', $token->getMatchedText());
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
