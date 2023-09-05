<?php

namespace testsuites\Parser;

use Mailcode\Mailcode_Parser_Statement;
use MailcodeTestCase;

/**
 * @package MailcodeTests
 * @subpackage Parser
 * @covers \Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_LegacySyntaxConversion
 */
final class LegacyConversionTests extends MailcodeTestCase
{
    public function test_timezoneToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('timezone: "Europe/Paris"');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName('timezone'));
    }

    public function test_breakAtToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('break-at: 42');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName('break-at'));
    }

    public function test_countToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('count: $FOO');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName('count'));
    }
}
