<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

final class Mailcode_IfTests extends MailcodeTestCase
{
    public function test_validation_passthru()
    {
        $tests = array(
            array(
                'label' => 'No validation.',
                'string' => '{if: $FOOBAR * "String" == 6 / 14}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $message = '';
            
            if(!$collection->isValid())
            {
                $message = $collection->getFirstError()->getMessage();
            }
            
            $label = $test['label'].' '.$message.PHP_EOL.$test['string'];
            
            $this->assertSame($test['valid'], $collection->isValid(), $label);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $label);
            }
        }
    }
    
   /**
    * Check for the issue where a zero in a command 
    * would be stripped out when normalized.
    */
    public function test_normalize_zero()
    {
        $cmd = Mailcode_Factory::if("0 == 1");
        
        $this->assertEquals('{if: 0 == 1}', $cmd->getNormalized());
    }
}
