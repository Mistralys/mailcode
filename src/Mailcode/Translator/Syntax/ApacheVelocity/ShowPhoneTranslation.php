<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowPhone;
use Mailcode\Mailcode_Translator_Command_ShowPhone;
use Mailcode\Translator\Syntax\ApacheVelocity;
use function Mailcode\dollarize;
use function Mailcode\undollarize;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowPhone} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowPhoneTranslation extends ApacheVelocity implements Mailcode_Translator_Command_ShowPhone
{
    public function translate(Mailcode_Commands_Command_ShowPhone $command): string
    {
        $template = "phone.e164(%s, '%s')";

        $varName = undollarize($command->getVariableName());
        $format = $command->getSourceFormat();

        $statement = sprintf(
            $template,
            dollarize($varName),
            $format
        );

        return $this->renderVariableEncodings($command, $statement);
    }
}
