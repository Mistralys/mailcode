<?php

namespace testsuites\Parser;

use Mailcode\Interfaces\Commands\Validation\BreakAtInterface;
use Mailcode\Interfaces\Commands\Validation\CountInterface;
use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
use Mailcode\Mailcode_Parser_Statement;
use MailcodeTestCase;

/**
 * @package MailcodeTests
 * @subpackage Parser
 * @covers \Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_LegacySyntaxConversion
 */
final class LegacyConversionTests extends MailcodeTestCase
{
    public function test_legacyTimezoneKeywordToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('timezone: "Europe/Paris"');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName(TimezoneInterface::PARAMETER_NAME));
    }

    public function test_legacyBreakAtKeywordToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('break-at=42');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName(BreakAtInterface::PARAMETER_NAME));
    }

    public function test_legacyCountKeywordToParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('count: $FOO');

        $info = $statement->getInfo();

        $this->assertNotNull($info->getTokenByParamName(CountInterface::PARAMETER_NAME));
    }
}
