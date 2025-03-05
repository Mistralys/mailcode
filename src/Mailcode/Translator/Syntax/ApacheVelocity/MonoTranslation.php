<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_Mono;
use Mailcode\Mailcode_Translator_Command_Mono;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_Mono} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class MonoTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_Mono
{
    public function translate(Mailcode_Commands_Command_Mono $command): string
    {
        // We do not want the command to be shown in the resulting
        // translated text. The preprocessor must be used if these
        // commands are to be transformed in the document.
        return '';
    }
}
