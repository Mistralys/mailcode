<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Translator\Syntax\ApacheVelocity;
use Mailcode\Translator\Syntax\HubL;
use Mailcode\Translator\Command\ShowEncodedInterface;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowEncoded} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowEncodedTranslation extends HubL implements ShowEncodedInterface
{
    public function translate(Mailcode_Commands_Command_ShowEncoded $command) : string
    {
        $stringLiteral = $this->renderQuotedValue($command->getText());

        return $this->renderEncodings($command, $stringLiteral);
    }
}
