<?php
/**
 * File containing the {@see Mailcode_Translator_Command_For} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_For
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "For" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_For
{
    public function translate(Mailcode_Commands_Command_For $command) : string;
}
