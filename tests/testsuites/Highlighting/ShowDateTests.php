<?php

declare(strict_types=1);

namespace MailcodeTests\Highlighting;

use Mailcode\Mailcode;
use MailcodeTestCase;

final class ShowDateTests extends MailcodeTestCase
{
    public function test_normalizeShowDate() : void
    {
        $tests = array(
            array(
                'label' => 'Show date',
                'string' => '{showdate: $FOOBAR}',
                'expected' =>
                    '<span class="mailcode-bracket">{</span>' .
                    '<span class="mailcode-command-name">showdate</span>' .
                    '<span class="mailcode-hyphen">:</span><wbr>' .
                    ' ' .
                    '<span class="mailcode-params">' .
                    '<span class="mailcode-token-variable">$FOOBAR</span>' .
                    '</span>' .
                    '<span class="mailcode-bracket">}</span>'
            ),
            array(
                'label' => 'Show date with date and timezone',
                'string' => '{showdate: $FOOBAR "Y-m-d" timezone="Europe/Berlin"}',
                'expected' =>
                    '<span class="mailcode-bracket">{</span>' .
                    '<span class="mailcode-command-name">showdate</span>' .
                    '<span class="mailcode-hyphen">:</span><wbr>' .
                    ' ' .
                    '<span class="mailcode-params">' .
                    '<span class="mailcode-token-variable">$FOOBAR</span>' .
                    ' ' .
                    '<span class="mailcode-token-stringliteral">"Y-m-d"</span>' .
                    ' ' .
                    '<span class="mailcode-token-paramname">timezone=</span>' .
                    '<span class="mailcode-token-stringliteral">"Europe/Berlin"</span>' .
                    '</span>' .
                    '<span class="mailcode-bracket">}</span>'
            )
        );

        $parser = Mailcode::create()->getParser();

        foreach ($tests as $test) {
            $result = $parser->parseString($test['string']);

            $command = $result
                ->getCollection()
                ->getFirstCommand();

            $this->assertNotNull($command);
            $this->assertEquals($test['expected'], $command->getHighlighted(), $test['label']);
        }
    }
}
