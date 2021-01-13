<?php
/**
 * File containing the {@see Mailcode_Translator_Command_Break} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_Break
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "Break" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_Break
{
    public function translate(Mailcode_Commands_Command_Break $command) : string;
}
