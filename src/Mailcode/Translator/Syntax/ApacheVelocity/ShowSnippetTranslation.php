<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowSnippet;
use Mailcode\Mailcode_Translator_Command_ShowSnippet;
use Mailcode\Translator\Syntax\ApacheVelocity;
use function Mailcode\undollarize;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowSnippet} command to Apache Velocity.
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
class ShowSnippetTranslation extends ApacheVelocity implements Mailcode_Translator_Command_ShowSnippet
{
    public function translate(Mailcode_Commands_Command_ShowSnippet $command): string
    {
        $varName = undollarize($command->getVariableName());

        if ($command->isNamespacePresent()) {
            $namespace = $command->getNamespaceToken()->getText();
            $statement = sprintf('dictionary.namespace("%s").name("%s")', $namespace, $varName);
        } else {
            $statement = sprintf('dictionary.global("%s")', $varName);
        }

        $statement = $command->isHTMLEnabled()
            ? sprintf('%s.replaceAll($esc.newline, "<br/>")', $statement)
            : sprintf('%s', $statement);

        return $this->renderVariableEncodings($command, $statement);
    }
}
