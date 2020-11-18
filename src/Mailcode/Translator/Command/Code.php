<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Command_Code} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Command_Code
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "Code" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_Code
{
    public function translate(Mailcode_Commands_Command_Code $command) : string;
}
