<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_Comment} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_Comment
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "Comment" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_Comment extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_Comment
{
    public function translate(Mailcode_Commands_Command_Comment $command): string
    {
        return '## '.$command->getComment();
    }
}
