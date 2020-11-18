<?php

use Mailcode\Mailcode_Commands_Command;

final class Mailcode_CommentTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{comment:    }',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Without quotes',
                'string' => '{comment: I noted something here so yeah!}',
                'normalized' => '{comment: I noted something here so yeah!}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With quotes',
                'string' => '{comment: I noted "something here" so yeah!}',
                'normalized' => '{comment: I noted "something here" so yeah!}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With special characters and tokens',
                'string' => '{comment: haha: $FOOBAR \\}',
                'normalized' => '{comment: haha: $FOOBAR \\}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
