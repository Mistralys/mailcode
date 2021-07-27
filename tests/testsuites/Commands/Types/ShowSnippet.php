<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowSnippet;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

final class Mailcode_ShowSnippetTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showsnippet:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{showsnippet: foobar}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showsnippet: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable',
                'string' => '{showsnippet: $foo_bar}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getVariable()
    {
        $snippet = Mailcode_Factory::show()->snippet('foobar');
        
        $this->assertEquals('$foobar', $snippet->getVariable()->getFullName());
        $this->assertEquals('$foobar', $snippet->getVariableName());
    }

    public function test_urlencode() : void
    {
        $cmd = Mailcode::create()->parseString('{showsnippet: $FOO urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());

        $this->assertEquals('{showsnippet: $FOO urlencode:}', Mailcode_Factory::show()->snippet('$FOO')->setURLEncoding(true)->getNormalized());
    }

    public function test_urldecode() : void
    {
        $cmd = Mailcode::create()->parseString('{showsnippet: $FOO urldecode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLDecoded());

        $this->assertEquals('{showsnippet: $FOO urldecode:}', Mailcode_Factory::show()->snippet('$FOO')->setURLDecoding(true)->getNormalized());
    }

    /**
     * Default behavior is to have HTML enabled.
     */
    public function test_defaultWithHTML() : void
    {
        $cmd = Mailcode_Factory::show()->snippet('snippetname');

        $this->assertNull($cmd->getNoHTMLToken());
        $this->assertTrue($cmd->isHTMLEnabled());
    }

    public function test_factory_noHTML() : void
    {
        $cmd = Mailcode_Factory::show()
            ->snippet('snippetname')
            ->setHTMLEnabled(false);

        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Keyword::class, $cmd->getNoHTMLToken());
        $this->assertFalse($cmd->isHTMLEnabled());
    }

    public function test_parse_noHTML() : void
    {
        $cmd = Mailcode::create()->parseString('{showsnippet: $FOO nohtml:}')->getFirstCommand();

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowSnippet::class, $cmd);
        $this->assertFalse($cmd->isHTMLEnabled());
    }
}
