<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Command_ShowPhone} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Command_ShowPhone
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowPhone" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ShowPhone
{
    public function translate(Mailcode_Commands_Command_ShowPhone $command) : string;
}
