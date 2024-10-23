<?php

declare(strict_types=1);

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

/**
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowSnippetTranslation
 */
final class Translator_Velocity_ShowSnippetTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Show snippet',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname'),
                'expected' => '${dictionary.global("snippetname").replaceAll($esc.newline, "<br/>")}'
            ),
            array(
                'label' => 'Show snippet, with URL encoding',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setURLEncoding(true),
                'expected' => '${esc.url($dictionary.global("snippetname").replaceAll($esc.newline, "<br/>"))}'
            ),
            array(
                'label' => 'Show snippet, with URL decoding',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setURLDecoding(true),
                'expected' => '${esc.unurl($dictionary.global("snippetname").replaceAll($esc.newline, "<br/>"))}'
            ),
            array(
                'label' => 'Show snippet, no HTML',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setHTMLEnabled(false),
                'expected' => '${dictionary.global("snippetname")}'
            ),
            array(
                'label' => 'Show snippet, no HTML, URL encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setHTMLEnabled(false)
                    ->setURLEncoding(true),
                'expected' => '${esc.url($dictionary.global("snippetname"))}'
            ),
            array(
                'label' => 'Show snippet, no HTML, with namespace',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname', 'my_namespace')
                    ->setHTMLEnabled(false),
                'expected' => '${dictionary.namespace("my_namespace").name("snippetname")}'
            ),
            array(
                'label' => 'Show snippet, no HTML, with namespace, URL encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname', 'my_namespace')
                    ->setHTMLEnabled(false)
                    ->setURLEncoding(true),
                'expected' => '${esc.url($dictionary.namespace("my_namespace").name("snippetname"))}'
            )
        );
        
        $this->runCommands($tests);
    }
}
