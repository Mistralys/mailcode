<?php
/**
 * File containing the interface {@see \Mailcode\Translator\Command\ShowEncodedInterface}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Command\ShowEncodedInterface
 */

declare(strict_types=1);

namespace Mailcode\Translator\Command;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;

/**
 * Interface for translators of the "ShowEncoded" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface ShowEncodedInterface
{
    public function translate(Mailcode_Commands_Command_ShowEncoded $command) : string;
}
