<?php

use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ElseIfEqualsNumberTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if: 1 == 1}{elseif equals-number: "1"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'No value specified',
                'string' => '{if: 1 == 1}{elseif equals-number: $FOOBAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'Invalid value specified',
                'string' => '{if: 1 == 1}{elseif equals-number: $FOOBAR "Not a number"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_NOT_NUMERIC
            ),
            array(
                'label' => 'Valid number as number',
                'string' => '{if: 1 == 1}{elseif equals-number: $FOOBAR 14554}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid number as string',
                'string' => '{if: 1 == 1}{elseif equals-number: $FOOBAR "18789"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid number as multiplication',
                'string' => '{if: 1 == 1}{elseif equals-number: $FOOBAR 1 * 2}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
