<?php
/**
 * File containing the {@see \Mailcode\Translator\BaseCommandTranslation} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\BaseCommandTranslation
 */

declare(strict_types=1);

namespace Mailcode\Translator;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Mailcode_Commands_Command;

/**
 * Abstract base class for syntax command translators.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @method string translate(Mailcode_Commands_Command $command)
 */
abstract class BaseCommandTranslation
{
    abstract public function getLabel() : string;

    abstract public function getSyntaxName() : string;

    protected function hasVariableEncodings(Mailcode_Commands_Command $command) : bool
    {
        return $command instanceof EncodableInterface && $command->hasActiveEncodings();
    }
}
