<?php

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ElseIfVariableTests extends MailcodeTestCase
{
    public function test_validation_variable()
    {
        $tests = array(
            array(
                'label' => 'With invalid variable',
                'string' => '{if: 1 == 1}{elseif variable: foobar}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'No operand after variable',
                'string' => '{if: 1 == 1}{elseif variable: $FOO "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_OPERAND_MISSING
            ),
            array(
                'label' => 'Using assignment, not comparison',
                'string' => '{if: 1 == 1}{elseif variable: $FOO = "Some text"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
            ),
            array(
                'label' => 'Without comparison value',
                'string' => '{if: 1 == 1}{elseif variable: $FOO == }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if: 1 == 1}{elseif variable: $FOO == "Something"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
