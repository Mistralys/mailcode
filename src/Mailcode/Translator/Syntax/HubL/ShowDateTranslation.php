<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Translator_Command_ShowDate;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowDate} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowDateTranslation extends HubL implements Mailcode_Translator_Command_ShowDate
{
    public function translate(Mailcode_Commands_Command_ShowDate $command): string
    {
        return '{# ! show date commands are not implemented ! #}';

        // TODO https://developers.hubspot.com/docs/cms/hubl/filters#format-datetime
//        return sprintf('{{ %s | format_datetime() }}',
//            $command->getVariableName()
//        );
    }
}
