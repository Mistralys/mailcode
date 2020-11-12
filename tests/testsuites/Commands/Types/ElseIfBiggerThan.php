<?php

use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ElseIfBiggerThanTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if: 1 == 1}{elseif bigger-than: "1"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'No value specified',
                'string' => '{if: 1 == 1}{elseif bigger-than: $FOOBAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'Invalid value specified',
                'string' => '{if: 1 == 1}{elseif bigger-than: $FOOBAR "Not a number"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_NOT_NUMERIC
            ),
            array(
                'label' => 'Valid number as number',
                'string' => '{if: 1 == 1}{elseif bigger-than: $FOOBAR 14554}{end}',
                'valid' => true,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Valid number as string',
                'string' => '{if: 1 == 1}{elseif bigger-than: $FOOBAR "18789"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid number as multiplication',
                'string' => '{if: 1 == 1}{elseif bigger-than: $FOOBAR 1 * 2}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
