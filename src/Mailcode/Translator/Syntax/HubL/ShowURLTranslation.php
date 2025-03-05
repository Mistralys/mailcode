<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use AppUtils\ConvertHelper;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowURL;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;
use Mailcode\Translator\Command\ShowURLInterface;
use testsuites\Translator\HubL\ShowURLTests;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowURL} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see ShowURLTests
 */
class ShowURLTranslation extends BaseHubLCommandTranslation implements ShowURLInterface
{
    public function translate(Mailcode_Commands_Command_ShowURL $command) : string
    {
        return $this->resolveURL($command);
    }

    private function resolveURL(Mailcode_Commands_Command_ShowURL $command) : string
    {
        // Remove newlines in the content.
        $content = trim(str_replace(array("\r", "\n"), '', $command->getContent()));

        $safeguard = Mailcode::create()->createSafeguard($content);

        return Mailcode::create()->createTranslator()
            ->createSyntax($this->getSyntaxName())
            ->translateSafeguard($safeguard);
    }
}
