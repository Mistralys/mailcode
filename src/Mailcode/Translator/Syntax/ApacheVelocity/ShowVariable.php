<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ShowVariable" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowVariable
{
    public function translate(Mailcode_Commands_Command_ShowVariable $command): string
    {
        $varName = ltrim($command->getVariableName(), '$');

        // Automatically URL escape the variable if it's in an URL.
        if($command->isURLEncoded())
        {
            return sprintf(
                '${esc.url($%s)}',
                $varName
            );
        }

        if($command->isURLDecoded())
        {
            return sprintf(
                '${esc.unurl($%s)}',
                $varName
            );
        }

        return sprintf(
            '${%s}',
            $varName
        );
    }
}
