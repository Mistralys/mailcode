<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Command_Mono} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Command_Mono
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "Mono" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_Mono
{
    public function translate(Mailcode_Commands_Command_Mono $command) : string;
}
