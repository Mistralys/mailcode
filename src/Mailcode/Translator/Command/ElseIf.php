<?php
/**
 * File containing the {@see Mailcode_Translator_Command_ElseIf} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_ElseIf
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ElseIf" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ElseIf
{
    public function translate(Mailcode_Commands_Command_ElseIf $command) : string;
}
