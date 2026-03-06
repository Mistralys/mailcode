<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

/**
 * Regression guard: all ShowSnippet variants must translate to the canonical
 * HubL not-supported comment, since HubL has no server-side dictionary
 * infrastructure to resolve snippet names.
 *
 * @see \Mailcode\Translator\Syntax\HubL\ShowSnippetTranslation
 */
final class ShowSnippetTests extends HubLTestCase
{
    public function test_basic(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, basic',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET'),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }

    public function test_urlEncoding(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, URL encoding',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET')->setURLEncoding(true),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }

    public function test_urlDecoding(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, URL decoding',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET')->setURLDecoding(true),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }

    public function test_noHTML(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, no HTML',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET')->setHTMLEnabled(false),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }

    public function test_withNamespace(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, with namespace',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET', 'ns'),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }

    public function test_noHTMLAndURLEncoding(): void
    {
        $this->runCommands(array(
            array(
                'label' => 'Show snippet, no HTML + URL encoding',
                'mailcode' => Mailcode_Factory::show()->snippet('SNIPPET')->setHTMLEnabled(false)->setURLEncoding(true),
                'expected' => self::buildNotSupportedComment('showsnippet')
            )
        ));
    }
}
