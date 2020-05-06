<?php
/**
 * File containing the {@see Mailcode_Translator_Command_ShowDate} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Command_ShowDate
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowDate" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Translator_Command_ShowDate
{
    public function translate(Mailcode_Commands_Command_ShowDate $command) : string;
}
