<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowPhone} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowPhone
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ShowNumber" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowPhone extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowPhone
{
    public function translate(Mailcode_Commands_Command_ShowPhone $command): string
    {
        $template = "phone.e164(%s, '%s')";

        $varName = ltrim($command->getVariableName(), '$');
        $format = $command->getSourceFormat();

        $statement = sprintf(
            $template,
            '$'.$varName,
            $format
        );

        return $this->addURLEncoding($command, $statement);
    }
}
