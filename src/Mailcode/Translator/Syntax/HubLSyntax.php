<?php

declare(strict_types=1);

namespace Mailcode\Translator\Syntax;

use Mailcode\Translator\BaseSyntax;

class HubLSyntax extends BaseSyntax
{
    public const SYNTAX_NAME = 'HubL';

    public function getLabel(): string
    {
        return 'Hubspot HubL';
    }

    public function getTypeID(): string
    {
        return self::SYNTAX_NAME;
    }
}
