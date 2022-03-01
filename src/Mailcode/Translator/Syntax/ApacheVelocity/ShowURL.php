<?php

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use AppUtils\ConvertHelper;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowURL;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity;
use Mailcode\Translator\Command\ShowURLInterface;

class ShowURL extends Mailcode_Translator_Syntax_ApacheVelocity implements ShowURLInterface
{
    public function translate(Mailcode_Commands_Command_ShowURL $command) : string
    {
        $statements = array();
        $statements[] = $this->renderURL($command);

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
            '$tracking.%s',
            implode('.', $statements)
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
            "lt(\${tracking_host}, \${envelope_hash}, %s)",
            $this->renderQuotedValue($command->getTrackingID())
        );
    }

    private function renderURL(Mailcode_Commands_Command_ShowURL $command) : string
    {
        return sprintf(
            'url(%s)',
            $this->renderQuotedValue($this->resolveURL($command))
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
