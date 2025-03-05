<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_End;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Commands_IfBase;
use Mailcode\Mailcode_Interfaces_Commands_PreProcessing;
use Mailcode\Mailcode_Translator_Command_End;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_End} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class EndTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_End
{
    public function translate(Mailcode_Commands_Command_End $command): string
    {
        $openCmd = $command->getOpeningCommand();

        // This ending command is tied to a preprocessing command: Since
        // we do not want to keep these, we return an empty string to strip
        // it out.
        if($openCmd instanceof Mailcode_Interfaces_Commands_PreProcessing) {
            return '';
        }

        if($openCmd instanceof Mailcode_Commands_Command_For) {
            return '{% endfor %}';
        }

        if($openCmd instanceof Mailcode_Commands_IfBase) {
            return '{% endif %}';
        }

        return '{# ! Unknown opening command for end command ! #}';
    }
}
