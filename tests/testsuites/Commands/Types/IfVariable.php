<?php

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Factory;

final class Mailcode_IfVariableTests extends MailcodeTestCase
{
    public function test_validation_variable()
    {
        $tests = array(
            array(
                'label' => 'With invalid variable',
                'string' => '{if variable: foobar}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'No operand after variable',
                'string' => '{if variable: $FOO "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_OPERAND_MISSING
            ),
            array(
                'label' => 'Using assignment, not comparison',
                'string' => '{if variable: $FOO = "Some text"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
            ),
            array(
                'label' => 'Without comparison value',
                'string' => '{if variable: $FOO == }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if variable: $FOO == "Something"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement, case insensitive',
                'string' => '{if variable: $FOO == "Something" insensitive:}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }

    public function test_notCaseInsensitive() : void
    {
        $command = Mailcode_Factory::if()->varEquals('FOOBAR', 'Some Text', true);

        $this->assertFalse($command->isCaseInsensitive());
    }

    public function test_caseInsensitive() : void
    {
        $command = Mailcode_Factory::if()->varEquals('FOOBAR', 'Some Text', true, true);

        $this->assertTrue($command->isCaseInsensitive());
    }
}
