<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_Break;
use Mailcode\Mailcode_Translator_Command_Break;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * HubL does not support the {@see Mailcode_Commands_Command_Break} command.
 *
 * HubL for-loops do not support early-exit (`{break}`). There is no equivalent
 * HubL statement. This class acts as a stub returning a not-supported comment,
 * following the same pattern as {@see ShowSnippetTranslation}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class BreakTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_Break
{
    /**
     * NOTE: This method is dead code. The Break command is listed in
     * {@see HubLSyntax::getUnsupportedCommands()} and is handled by
     * BaseSyntax::translateCommand() before this class is ever reached.
     *
     * @internal
     * @codeCoverageIgnore
     */
    public function translate(Mailcode_Commands_Command_Break $command): string
    {
        return '{# !break is not supported in HubL! #}';
    }
}
