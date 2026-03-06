<?php

/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax;

use Mailcode\Translator\BaseSyntax;

/**
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
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

    /**
     * @return string[]
     */
    protected function getUnsupportedCommands() : array
    {
        return array('Break', 'ShowSnippet');
    }
}
