<?php
/**
 * File containing the {@see Mailcode_Translator_Command_Comment} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_Comment
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "Comment" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_Comment
{
    public function translate(Mailcode_Commands_Command_Comment $command) : string;
}
