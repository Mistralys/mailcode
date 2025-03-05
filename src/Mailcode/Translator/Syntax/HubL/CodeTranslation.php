<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Translator_Command_Code;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_Code} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class CodeTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_Code
{
    private string $template = <<<'EOD'
{#
 Wrapper IF for the code command to insert native %1$s commands.
 This is needed for technical Mailcode reasons. 
#}
{% if true %}%2$s{% endif %}
EOD;

    public function translate(Mailcode_Commands_Command_Code $command): string
    {
        return sprintf(
            $this->template,
            $command->getSyntaxName(),
            $command->getContentTrimmed()
        );
    }
}
