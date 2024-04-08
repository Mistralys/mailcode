<?php

declare(strict_types=1);

use Mailcode\Mailcode_Factory;

/**
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowSnippetTranslation
 */
final class Translator_Velocity_ShowSnippetTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Show snippet',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname'),
                'expected' => '${snippetname.replaceAll($esc.newline, "<br/>")}'
            ),
            array(
                'label' => 'Show snippet, with URL encoding',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setURLEncoding(true),
                'expected' => '${esc.url($snippetname.replaceAll($esc.newline, "<br/>"))}'
            ),
            array(
                'label' => 'Show snippet, with URL decoding',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setURLDecoding(true),
                'expected' => '${esc.unurl($snippetname.replaceAll($esc.newline, "<br/>"))}'
            ),
            array(
                'label' => 'Show snippet, no HTML',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setHTMLEnabled(false),
                'expected' => '${snippetname}'
            ),
            array(
                'label' => 'Show snippet, no HTML, URL encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->snippet('$snippetname')
                    ->setHTMLEnabled(false)
                    ->setURLEncoding(true),
                'expected' => '${esc.url($snippetname)}'
            )
        );
        
        $this->runCommands($tests);
    }
}
