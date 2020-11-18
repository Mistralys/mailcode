<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_For} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_For
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "For" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_Code extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_Code
{
    public function translate(Mailcode_Commands_Command_Code $command): string
    {
        $syntax = $command->getSyntaxName();

        return <<<EOD
#**
 Wrapper IF for the code command to insert native $syntax commands.
 This is needed for technical Mailcode reasons. 
*#
#if(true)
EOD;
    }
}
