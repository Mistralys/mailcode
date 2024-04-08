<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowSnippet;
use Mailcode\Mailcode_Translator_Command_ShowSnippet;
use Mailcode\Translator\Syntax\HubL;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowSnippet} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowSnippetTranslation extends HubL implements Mailcode_Translator_Command_ShowSnippet
{
    public function translate(Mailcode_Commands_Command_ShowSnippet $command): string
    {
        return sprintf(
            '{{ %s }}',
            $this->renderEncodings($command, $this->formatVariableName($command->getVariableName()))
        );
    }
}
