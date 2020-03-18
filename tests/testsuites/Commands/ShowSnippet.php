<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowSnippet;

final class Mailcode_ShowSnippetTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showsnippet:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{showsnippet: foobar}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showsnippet: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowSnippet::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable',
                'string' => '{showsnippet: $foo_bar}',
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
}
