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
            ),
            array(
                'label' => 'Velocity syntax',
                'string' => '{if: 1 == 1}{elseif: $FOOBAR.urldecode().matches(".*[?].*")}{end}',
                'normalized' => '{if: 1 == 1}{elseif: $FOOBAR.urldecode().matches(".*[?].*")}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Escaping brackets',
                'string' => '{if: 1 == 1}{elseif: $FOOBAR "\{test\}"}{end}',
                'normalized' => '{if: 1 == 1}{elseif: $FOOBAR "\{test\}"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
