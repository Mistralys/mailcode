<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

final class Mailcode_EndTests extends MailcodeTestCase
{
    public function test_validation()
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
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $this->assertSame($test['valid'], $collection->isValid(), $test['label']);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $test['label']);
            }
        }
    }
    
    public function test_highlight()
    {
        $end = Mailcode_Factory::end();
        
        $expected = '<span class="mailcode-bracket">{</span><span class="mailcode-command-name">end</span><span class="mailcode-bracket">}</span>';
        
        $this->assertSame($expected, $end->getHighlighted());
    }
}
