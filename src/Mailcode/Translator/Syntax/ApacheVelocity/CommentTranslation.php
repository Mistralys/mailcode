<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_Comment;
use Mailcode\Mailcode_Translator_Command_Comment;
use Mailcode\Translator\Syntax\ApacheVelocity;

/**
 * Translates the {@see Mailcode_Commands_Command_Comment} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class CommentTranslation extends ApacheVelocity implements Mailcode_Translator_Command_Comment
{
    public function translate(Mailcode_Commands_Command_Comment $command): string
    {
        return PHP_EOL.
        '#**'.PHP_EOL.
        '  '.$command->getCommentString().PHP_EOL.
        '*#'.PHP_EOL;
    }
}
