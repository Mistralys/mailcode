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

use AppUtils\ConvertHelper;

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
        $varName = ltrim($command->getVariableName(), '$');
        $javaFormat = $this->translateFormat($command->getFormatString());

        if($command->isURLEncoded())
        {
            return sprintf(
                '${esc.url($number.format(\'%s\', $number.toNumber(\'##.##\', $%s)))}',
                $javaFormat,
                $varName
            );
        }

        return sprintf(
            '${number.format(\'%s\', $number.toNumber(\'##.##\', $%s))}',
            $javaFormat,
            $varName
        );
    }

    private function translateFormat(string $formatString) : string
    {
        return '###,###.##';
    }
}
