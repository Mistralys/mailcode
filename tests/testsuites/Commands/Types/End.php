<?php

use Mailcode\Mailcode_Factory;

final class Mailcode_EndTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{if variable: $FOO == "Value"}{
    end

}',
                'valid' => true,
                'code' => 0
            ),
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_highlight() : void
    {
        $end = Mailcode_Factory::misc()->end();
        
        $expected = '<span class="mailcode-bracket">{</span><span class="mailcode-command-name">end</span><span class="mailcode-bracket">}</span>';
        
        $this->assertSame($expected, $end->getHighlighted());
    }
}
