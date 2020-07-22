<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Date_FormatInfo;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ShowDateTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showdate:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{showdate: foobar}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showdate: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable, omitting format string',
                'string' => '{showdate: $foo_bar}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and empty format string',
                'string' => '{showdate: $foo_bar ""}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_EMPTY_FORMAT_STRING
            ),
            array(
                'label' => 'With valid variable and invalid format string',
                'string' => '{showdate: $foo_bar "Y-m-B"}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_INVALID_FORMAT_CHARACTER
            ),
            array(
                'label' => 'With valid variable and valid format string',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s"}',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $this->assertSame($test['valid'], $collection->isValid(), $test['label']);
            
            if(!$test['valid'])
            {
                $errors = $collection->getErrors();
                $this->assertSame($test['code'], $errors[0]->getCode(), $test['label']);
            }
        }
    }
    
    public function test_getVariable()
    {
        $cmd = Mailcode_Factory::showDate('foobar');
        
        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('$foobar', $cmd->getVariableName());
    }
}
