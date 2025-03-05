<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Translator_Command_ShowNumber;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowNumber} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowNumberTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_ShowNumber
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
