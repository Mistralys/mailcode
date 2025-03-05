<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_End;
use Mailcode\Mailcode_Interfaces_Commands_PreProcessing;
use Mailcode\Mailcode_Translator_Command_End;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_End} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class EndTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_End
{
    public function translate(Mailcode_Commands_Command_End $command): string
    {
        // This ending command is tied to a preprocessing command: Since
        // we do not want to keep these, we return an empty string to strip
        // it out.
        if($command->getOpeningCommand() instanceof Mailcode_Interfaces_Commands_PreProcessing) {
            return '';
        }

        return '#{end}';
    }
}
