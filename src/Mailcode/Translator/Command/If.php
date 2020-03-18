<?php
/**
 * File containing the {@see Mailcode_Translator_Command_If} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_If
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "If" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_If
{
    public function translate(Mailcode_Commands_Command_If $command) : string;
}
