<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_If;
use Mailcode\Mailcode_Translator_Command_If;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_If} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class IfTranslation extends HubL implements Mailcode_Translator_Command_If
{
    public function translate(Mailcode_Commands_Command_If $command): string
    {
        return '{# ! if commands are not implemented ! #}';
    }
}
