<?php

use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_IfEmptyTests extends MailcodeTestCase
{
    public function test_validation_empty()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if empty: $FOOBAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'No variable specified',
                'string' => '{if not-empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if not-empty: $FOOBAR}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
