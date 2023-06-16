<?php

declare(strict_types=1);

namespace MailcodeTests\Commands\Types;

use Mailcode\Interfaces\Commands\Validation\CountInterface;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Factory;
use MailcodeTestCase;

final class SetVarTests extends MailcodeTestCase
{
    public function test_validation() : void
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
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
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
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'With missing variable',
                'string' => '{setvar: = "Text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
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
            ),
            array(
                'label' => 'Omit the equals sign',
                'string' => '{setvar: $FOO.BAR 4 + 6}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Omit the equals sign',
                'string' => '{setvar: $FOO.BAR "My value"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Invalid operand beyond the equals sign',
                'string' => '{setvar: $FOO.BAR = 4 <= 6}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
            ),
            array(
                'label' => 'Keyword count has invalid parameter (unquoted text)',
                'string' => '{setvar: $FOO.BAR count: FOO.COUNT}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Keyword count has invalid parameter (text)',
                'string' => '{setvar: $FOO.BAR count: "Text"}',
                'valid' => false,
                'code' => CountInterface::VALIDATION_COUNT_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'Keyword count has invalid parameter (number)',
                'string' => '{setvar: $FOO.BAR count: 13}',
                'valid' => false,
                'code' => CountInterface::VALIDATION_COUNT_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'Valid count keyword with parameter',
                'string' => '{setvar: $FOO.BAR count: $FOO.COUNT}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Simple variable is valid',
                'string' => '{setvar: $COUNTER count: $FOO.COUNT}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Keyword count without parameter',
                'string' => '{setvar: $FOO.BAR count:}',
                'valid' => false,
                'code' => CountInterface::VALIDATION_COUNT_CODE_WRONG_TYPE
            )
        );

        $this->runCollectionTests($tests);
    }

    public function test_countParameter() : void
    {
        $cmd = Mailcode_Factory::set()->varCount('VAR_NAME', 'VAR_COUNT');
        $count = $cmd->getCountVariable();

        $this->assertTrue($cmd->isCountEnabled());
        $this->assertNotNull($count);
        $this->assertSame('$VAR_COUNT', $count->getFullName());
    }
}
