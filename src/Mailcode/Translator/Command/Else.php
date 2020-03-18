<?php
/**
 * File containing the {@see Mailcode_Translator_Command_Else} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_Else
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "Else" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_Else
{
    public function translate(Mailcode_Commands_Command_Else $command) : string;
}
