<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_SetVariable;

final class Mailcode_SetVarTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{setvar:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With double equals sign',
                'string' => '{setvar: $FOO.BAR == "Text"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_SetVariable::VALIDATION_NOT_ASSIGNMENT_STATEMENT
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{setvar: FOOBAR = "Text"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With missing value',
                'string' => '{setvar: $FOO.BAR = }',
                'valid' => false,
                'code' => Mailcode_Commands_Command_SetVariable::VALIDATION_NOT_ASSIGNMENT_STATEMENT
            ),
            array(
                'label' => 'With missing variable',
                'string' => '{setvar: = "Text"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_SetVariable::VALIDATION_NOT_ASSIGNMENT_STATEMENT
            ),
            array(
                'label' => 'With invalid string value',
                'string' => '{setvar: $FOO.BAR = Text}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With valid string value',
                'string' => '{setvar: $FOO.BAR = 4 + 6}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid string value',
                'string' => '{setvar: $FOO.BAR = "Text"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable value',
                'string' => '{setvar: $FOO.BAR = $OTHER.VAR}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable multiplication',
                'string' => '{setvar: $FOO.BAR = $OTHER.VAR * 2}',
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
