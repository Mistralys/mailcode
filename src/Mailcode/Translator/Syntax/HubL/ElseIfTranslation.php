<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ElseIf;
use Mailcode\Mailcode_Translator_Command_ElseIf;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_ElseIf} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ElseIfTranslation extends HubL implements Mailcode_Translator_Command_ElseIf
{
    public function translate(Mailcode_Commands_Command_ElseIf $command): string
    {
        return '{# ! elseif commands are not implemented ! #}';
    }
}
