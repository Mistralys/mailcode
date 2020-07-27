<?php

use Mailcode\Mailcode;

final class Mailcode_Highlighting_ShowDateTests extends MailcodeTestCase
{
    public function test_normalizeShowDate()
    {
        $tests = array(
            array(
                'label' => 'Show date',
                'string' => '{showdate: $FOOBAR}',
                'expected' => 
                    '<span class="mailcode-bracket">{</span>'.
                    '<span class="mailcode-command-name">showdate</span>'.
                    '<span class="mailcode-hyphen">:</span><wbr>'.
                    '<span class="mailcode-params">'.
                        ' '.
                        '<span class="mailcode-token-variable">$FOOBAR</span>'.
                    '</span>'.
                    '<span class="mailcode-bracket">}</span>'
            ),
            array(
                'label' => 'Show date with date',
                'string' => '{showdate: $FOOBAR "Y-m-d"}',
                'expected' => 
                    '<span class="mailcode-bracket">{</span>'.
                    '<span class="mailcode-command-name">showdate</span>'.
                    '<span class="mailcode-hyphen">:</span><wbr>'.
                    '<span class="mailcode-params">'.
                        ' '.
                        '<span class="mailcode-token-variable">$FOOBAR</span>'.
                        ' '.
                        '<span class="mailcode-token-stringliteral">"Y-m-d"</span>'.
                    '</span>'.
                    '<span class="mailcode-bracket">}</span>'
            ),
            array(
                'label' => 'Show date with date and time',
                'string' => '{showdate: $FOOBAR "Y-m-d H:i:s"}',
                'expected' => '{showdate: $FOOBAR "Y-m-d H:i:s"}'
            )
        );
        
        $parser = Mailcode::create()->getParser();
        
        foreach($tests as $test)
        {
            $result = $parser->parseString($test['string']);
            
            $command = $result->getFirstCommand();
            
            $this->assertEquals($test['expected'], $command->getHighlighted(), $test['label']);
        }
    }
}
