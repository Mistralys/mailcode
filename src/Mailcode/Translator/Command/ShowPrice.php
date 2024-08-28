<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Command_ShowPrice} interface.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Command_ShowPrice
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for translators of the "ShowPrice" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 */
interface Mailcode_Translator_Command_ShowPrice
{
    public function translate(Mailcode_Commands_Command_ShowPrice $command): string;
}
