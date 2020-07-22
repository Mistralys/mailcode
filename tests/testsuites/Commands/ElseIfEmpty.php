<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ElseIfEmptyTests extends MailcodeTestCase
{
    public function test_validation_notEmpty()
    {
        $tests = array(
            array(
                'label' => 'Empty: No variable specified',
                'string' => '{if: 1 = 1}{elseif empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Empty: Valid statement',
                'string' => '{if: 1 = 1}{elseif empty: $FOOBAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Not empty: No variable specified',
                'string' => '{if: 1 = 1}{elseif not-empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Not empty: Valid statement',
                'string' => '{if: 1 = 1}{elseif not-empty: $FOOBAR}{end}',
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
}
