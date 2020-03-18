<?php
/**
 * File containing the {@see Mailcode_Translator_Command_SetVariable} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_SetVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "SetVariable" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_SetVariable
{
    public function translate(Mailcode_Commands_Command_SetVariable $command) : string;
}
