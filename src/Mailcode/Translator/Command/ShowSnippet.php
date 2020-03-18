<?php
/**
 * File containing the {@see Mailcode_Translator_Command_ShowSnippet} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_ShowSnippet
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowSnippet" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ShowSnippet
{
    public function translate(Mailcode_Commands_Command_ShowSnippet $command) : string;
}
