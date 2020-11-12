<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Command_ShowNumber} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Command_ShowNumber
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowNumber" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ShowNumber
{
    public function translate(Mailcode_Commands_Command_ShowNumber $command) : string;
}
