<?php

use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_CommentTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{comment:    }',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_COMMENT_MISSING
            ),
            array(
                'label' => 'Without quotes',
                'string' => '{comment: I noted something here so yeah!}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With quotes',
                'string' => '{comment: I noted "something here" so yeah!}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With special characters and tokens',
                'string' => '{comment: haha: $FOOBAR \\}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
