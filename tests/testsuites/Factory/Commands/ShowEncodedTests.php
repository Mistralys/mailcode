<?php

declare(strict_types=1);

namespace testsuites\Factory\Commands;

use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Factory;
use MailcodeTestCase;

class ShowEncodedTests extends MailcodeTestCase
{
    public function test_idnEncode() : void
    {
        $cmd = Mailcode_Factory::show()->encoded(
            'Some text to encode',
            array(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE)
        );

        $this->assertTrue($cmd->isIDNEncoded());
    }

    public function test_multiEncode() : void
    {
        $encodings = array(
            Mailcode_Commands_Keywords::TYPE_IDN_ENCODE,
            Mailcode_Commands_Keywords::TYPE_URLENCODE
        );

        $cmd = Mailcode_Factory::show()->encoded(
            'Some text to encode',
            $encodings
        );

        $this->assertTrue($cmd->isIDNEncoded());
        $this->assertTrue($cmd->isURLEncoded());

        $this->assertSame($encodings, $cmd->getActiveEncodings());
    }
}
