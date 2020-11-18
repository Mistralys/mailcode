<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;

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
        $snippet = Mailcode_Factory::showSnippet('foobar');
        
        $this->assertEquals('$foobar', $snippet->getVariable()->getFullName());
        $this->assertEquals('$foobar', $snippet->getVariableName());
    }

    public function test_urlencode() : void
    {
        $cmd = Mailcode::create()->parseString('{showsnippet: $FOO urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());

        $this->assertEquals('{showsnippet: $FOO urlencode:}', Mailcode_Factory::showSnippet('$FOO')->setURLEncoding(true)->getNormalized());
    }

    public function test_urldecode() : void
    {
        $cmd = Mailcode::create()->parseString('{showsnippet: $FOO urldecode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLDecoded());

        $this->assertEquals('{showsnippet: $FOO urldecode:}', Mailcode_Factory::showSnippet('$FOO')->setURLDecoding(true)->getNormalized());
    }
}
