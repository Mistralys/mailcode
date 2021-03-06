<?php
/**
 * File containing the {@see Mailcode_Translator_Command_End} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_End
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "End" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_End
{
    public function translate(Mailcode_Commands_Command_End $command) : string;
}
