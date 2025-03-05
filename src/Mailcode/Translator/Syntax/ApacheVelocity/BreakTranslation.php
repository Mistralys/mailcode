<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_Break;
use Mailcode\Mailcode_Translator_Command_Break;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_Break} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class BreakTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_Break
{
    public function translate(Mailcode_Commands_Command_Break $command): string
    {
        return '#{break}';
    }
}
