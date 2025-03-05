<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_Break;
use Mailcode\Mailcode_Translator_Command_Break;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_Break} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class BreakTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_Break
{
    public function translate(Mailcode_Commands_Command_Break $command): string
    {
        return '{# !break is not supported in HubL! #}';
    }
}
