<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber
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
class Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowNumber
{
    public function translate(Mailcode_Commands_Command_ShowNumber $command): string
    {
        $statement = $this->renderNumberFormat(
            $command->getVariableName(),
            $command->getFormatInfo(),
            $command->isAbsolute()
        );

        return $this->renderVariableEncodings($command, $statement);
    }
}
