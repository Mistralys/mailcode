<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;
use MailcodeTestCase;

final class ShowVarTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showvar:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{showvar: foobar}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showvar: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable',
                'string' => '{showvar: $foo_bar}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and another parameter',
                'string' => '{showvar: $foo_bar "Text"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowVariable::VALIDATION_TOO_MANY_PARAMETERS
            ),
            array(
                'label' => 'With valid variable and decryption key',
                'string' => '{showvar: $foo_bar decrypt:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and custom decryption key',
                'string' => '{showvar: $foo_bar decrypt: "barfoo"}',
                'valid' => true,
                'code' => 0
            ),
        );

        $this->runCollectionTests($tests);
    }

    public function test_getVariable() : void
    {
        $cmd = Mailcode_Factory::show()->var('foobar');

        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('$foobar', $cmd->getVariableName());
    }

    /**
     * URL encoding, when set, must automatically add the keyword
     * to the command. This way it is present when the command is normalized.
     */
    public function test_urlEncodeFromParams() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO urlencode:}')->getFirstCommand();

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);
        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_urlEncodeFromSet() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO}')->getFirstCommand();
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);

        $cmd->setURLEncoding(true);
        $this->assertEquals('{showvar: $FOO urlencode:}', $cmd->getNormalized());

        $cmd->setURLEncoding(false);
        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }

    public function test_bothEncodeAndDecodeError() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO urlencode: urldecode:}');

        $this->assertFalse($cmd->isValid());
        $this->assertEquals(Mailcode_Commands_CommonConstants::VALIDATION_URL_DE_AND_ENCODE_ENABLED, $cmd->getFirstError()->getCode());
    }

    public function test_idnEncodeFromParams() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO idnencode:}')->getFirstCommand();

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);
        $this->assertTrue($cmd->isIDNEncoded());
    }

    public function test_idnEncodeFromSet() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO}')->getFirstCommand();
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);

        $cmd->setIDNEncoding(true);
        $this->assertEquals('{showvar: $FOO idnencode:}', $cmd->getNormalized());

        $cmd->setIDNEncoding(false);
        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }
}
