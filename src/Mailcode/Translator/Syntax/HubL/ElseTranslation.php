<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_Else;
use Mailcode\Mailcode_Translator_Command_Else;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_Else} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ElseTranslation extends HubL implements Mailcode_Translator_Command_Else
{
    public function translate(Mailcode_Commands_Command_Else $command): string
    {
        return '{% else %}';
    }
}
