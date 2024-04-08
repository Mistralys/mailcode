<?php
/**
 * File containing the {@see \Mailcode\Translator\Syntax\ApacheVelocity\ShowPhoneTranslation} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowPhoneTranslation
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowPhone;
use Mailcode\Mailcode_Translator_Command_ShowPhone;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowPhone} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowPhoneTranslation extends HubL implements Mailcode_Translator_Command_ShowPhone
{
    public function translate(Mailcode_Commands_Command_ShowPhone $command): string
    {
        return '{{ ' . $command->getVariableName() . ' }}';
    }
}
