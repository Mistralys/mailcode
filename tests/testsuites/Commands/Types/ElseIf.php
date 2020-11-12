<?php

final class Mailcode_ElseIfTests extends MailcodeTestCase
{
    public function test_validation_passthru()
    {
        $tests = array(
            array(
                'label' => 'No validation.',
                'string' => '{if: 1 == 1}{elseif: $FOOBAR * "String" == 6 / 14}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
