<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Translator_Command_For;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_For} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ForTranslation extends HubL implements Mailcode_Translator_Command_For
{
    public function translate(Mailcode_Commands_Command_For $command): string
    {
        return '{# ! for commands are not implemented ! #}';
    }
}
