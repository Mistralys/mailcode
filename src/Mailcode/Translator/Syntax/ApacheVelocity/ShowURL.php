<?php
/**
 * File containing the class {@see \Mailcode\Translator\Syntax\ApacheVelocity\ShowURL}.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowURL
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use AppUtils\ConvertHelper;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowURL;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity;
use Mailcode\Translator\Command\ShowURLInterface;

/**
 * Translates the `showurl` command to ApacheVelocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowURL extends Mailcode_Translator_Syntax_ApacheVelocity implements ShowURLInterface
{
    public const URL_VAR_TEMPLATE = 'url_tpl%03d';

    private static int $urlCounter = 0;


    public static function resetURLCounter() : void
    {
        self::$urlCounter = 0;
    }

    public function translate(Mailcode_Commands_Command_ShowURL $command) : string
    {
        self::$urlCounter++;

        $urlVar = sprintf(
            self::URL_VAR_TEMPLATE,
            self::$urlCounter
        );

        $statements = array();
        $statements[] = $this->renderURL($urlVar);

        if($command->isTrackingEnabled())
        {
            $statements[] = $this->renderTracking($command);
        }

        if($command->hasQueryParams())
        {
            $params = $command->getQueryParams();

            foreach($params as $name => $value)
            {
                $statements[] = $this->renderQueryParam($name, $value);
            }
        }

        return sprintf(
            '%s${tracking.%s}',
            $this->renderURLTemplate($command, $urlVar),
            implode('.', $statements)
        );
    }

    private function renderURLTemplate(Mailcode_Commands_Command_ShowURL $command, string $urlVar) : string
    {
        return sprintf(
            '#{define}($%s)%s#{end}',
            $urlVar,
            $this->resolveURL($command)
        );
    }

    private function renderQueryParam(string $name, string $value) : string
    {
        return sprintf(
            'query(%s, %s)',
            $this->renderQuotedValue($name),
            $this->renderQuotedValue($value)
        );
    }

    private function renderTracking(Mailcode_Commands_Command_ShowURL $command) : string
    {
        return sprintf(
            "lt(\${tracking_host}, \${envelope.hash}, %s)",
            $this->renderQuotedValue($command->getTrackingID())
        );
    }

    private function renderURL(string $urlVar) : string
    {
        return sprintf(
            'url(${%s})',
            $urlVar
        );
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
