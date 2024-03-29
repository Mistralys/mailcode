<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ShowSnippet" command to Apache Velocity.
 * 
 * NOTE: Requires the `EscapeTool` VTL tool to be enabled 
 * for the templates.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @see https://velocity.apache.org/tools/devel/apidocs/org/apache/velocity/tools/generic/EscapeTool.html
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowSnippet extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowSnippet
{
    public function translate(Mailcode_Commands_Command_ShowSnippet $command): string
    {
        $varName = undollarize($command->getVariableName());

        if($command->isHTMLEnabled())
        {
            $statement = sprintf(
                '%s.replaceAll($esc.newline, "<br/>")',
                $varName
            );
        }
        else
        {
            $statement = sprintf(
                '%s',
                $varName
            );
        }

        return $this->renderVariableEncodings($command, $statement);
    }
}
