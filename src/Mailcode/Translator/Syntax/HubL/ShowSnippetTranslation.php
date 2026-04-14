<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowSnippet;
use Mailcode\Mailcode_Translator_Command_ShowSnippet;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * HubL does not support the {@see Mailcode_Commands_Command_ShowSnippet} command.
 *
 * HubL has no server-side dictionary infrastructure to resolve snippet names,
 * so this command cannot be translated. A "not supported" stub comment is emitted
 * instead, following the same pattern as {@see BreakTranslation}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowSnippetTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_ShowSnippet
{
    /**
     * NOTE: This method is dead code. The ShowSnippet command is listed in
     * {@see HubLSyntax::getUnsupportedCommands()} and is handled by
     * BaseSyntax::translateCommand() before this class is ever reached.
     *
     * @internal
     * @codeCoverageIgnore
     */
    public function translate(Mailcode_Commands_Command_ShowSnippet $command): string
    {
        return '{# !showsnippet is not supported in HubL! #}';
    }
}
