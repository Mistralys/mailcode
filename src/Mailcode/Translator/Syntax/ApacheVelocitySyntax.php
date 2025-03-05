<?php

declare(strict_types=1);

namespace Mailcode\Translator\Syntax;

use Mailcode\Translator\BaseSyntax;

class ApacheVelocitySyntax extends BaseSyntax
{
    public const SYNTAX_NAME = 'ApacheVelocity';

    public function getLabel(): string
    {
        return 'Apache Velocity';
    }

    public function getTypeID(): string
    {
        return self::SYNTAX_NAME;
    }
}
