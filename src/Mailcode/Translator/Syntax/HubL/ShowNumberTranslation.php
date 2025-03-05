<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Translator_Command_ShowNumber;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowNumber} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowNumberTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_ShowNumber
{
    public function translate(Mailcode_Commands_Command_ShowNumber $command): string
    {
        return '{{ ' . $command->getVariableName() . ' }}';
    }
}
