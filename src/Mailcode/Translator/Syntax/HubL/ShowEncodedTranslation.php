<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;
use Mailcode\Translator\Command\ShowEncodedInterface;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowEncoded} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowEncodedTranslation extends BaseHubLCommandTranslation implements ShowEncodedInterface
{
    private static int $counter = 0;

    public function translate(Mailcode_Commands_Command_ShowEncoded $command) : string
    {
        self::$counter++;

        $stringLiteral = $this->renderQuotedValue($command->getText());
        $name = sprintf('literal%03d', self::$counter);

        $variable = '{% set '.$name.' = '.$stringLiteral.' %}';

        return $variable.'{{ '.$this->renderEncodings($command, $name).' }}';
    }

    public static function resetCounter() : void
    {
        self::$counter = 0;
    }
}
