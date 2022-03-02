<?php
/**
 * File containing the class {@see \Mailcode\Translator\Syntax\ApacheVelocity\ShowEncoded}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowEncoded
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity;
use Mailcode\Translator\Command\ShowEncodedInterface;

/**
 * Translates the `showencoded` command to ApacheVelocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowEncoded extends Mailcode_Translator_Syntax_ApacheVelocity implements ShowEncodedInterface
{
    public function translate(Mailcode_Commands_Command_ShowEncoded $command) : string
    {
        $stringLiteral = $this->renderQuotedValue($command->getText());

        return $this->renderEncodings($command, $stringLiteral);
    }
}
