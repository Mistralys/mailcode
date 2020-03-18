<?php
/**
 * File containing the {@see Mailcode_Translator_Command_ShowVariable} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowVariable" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ShowVariable
{
    public function translate(Mailcode_Commands_Command_ShowVariable $command) : string;
}
