<?php
/**
 * File containing the interface {@see \Mailcode\Translator\Command\ShowURLInterface}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Command\ShowURLInterface
 */

declare(strict_types=1);

namespace Mailcode\Translator\Command;

use Mailcode\Mailcode_Commands_Command_ShowURL;

/**
 * Interface for translators of the "ShowURL" command.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface ShowURLInterface
{
    public function translate(Mailcode_Commands_Command_ShowURL $command) : string;
}
