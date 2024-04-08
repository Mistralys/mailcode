<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Translator\Syntax\ApacheVelocity;
use Mailcode\Translator\Command\ShowEncodedInterface;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowEncoded} command to ApacheVelocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowEncodedTranslation extends ApacheVelocity implements ShowEncodedInterface
{
    public function translate(Mailcode_Commands_Command_ShowEncoded $command) : string
    {
        $stringLiteral = $this->renderQuotedValue($command->getText());

        return $this->renderEncodings($command, $stringLiteral);
    }
}
