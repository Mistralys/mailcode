<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Decrypt\DecryptSettings;
use Mailcode\Interfaces\Commands\Validation\DecryptInterface;
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
                'label' => 'With valid variable and another parameter, which is ignored',
                'string' => '{showvar: $foo_bar "Text"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and default decryption key',
                'string' => '{showvar: $foo_bar decrypt=""}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and custom decryption key',
                'string' => '{showvar: $foo_bar "barfoo" decrypt="fookey"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and custom decryption key',
                'string' => '{showvar: $foo_bar decrypt="fookey" idnencode:}',
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
        $cmd = $this->getCommandFromString('{showvar: $FOO urlencode:}');

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);
        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_urlEncodeFromSet() : void
    {
        $cmd = $this->getCommandFromString('{showvar: $FOO}');

        $cmd->setURLEncoding(true);
        $this->assertEquals('{showvar: $FOO urlencode:}', $cmd->getNormalized());

        $cmd->setURLEncoding(false);
        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }

    public function test_bothEncodeAndDecodeError() : void
    {
        $mailcode = Mailcode::create()->parseString('{showvar: $FOO urlencode: urldecode:}');

        $this->assertFalse($mailcode->isValid());
        $this->assertEquals(Mailcode_Commands_CommonConstants::VALIDATION_URL_DE_AND_ENCODE_ENABLED, $mailcode->getFirstError()->getCode());
    }

    public function test_idnEncodeFromParams() : void
    {
        $cmd = $this->getCommandFromString('{showvar: $FOO idnencode:}');

        $this->assertTrue($cmd->isIDNEncoded());
    }

    public function test_idnEncodeFromSet() : void
    {
        $cmd = $this->getCommandFromString('{showvar: $FOO}');

        $cmd->setIDNEncoding(true);
        $this->assertEquals('{showvar: $FOO idnencode:}', $cmd->getNormalized());

        $cmd->setIDNEncoding(false);
        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }

    public function test_decryptDefault() : void
    {
        DecryptSettings::setDefaultKeyName('my-key');

        $cmd = $this->getCommandFromString('{showvar: $FOO decrypt=""}');

        $this->assertTrue($cmd->isDecryptionEnabled());
        $this->assertEquals('my-key', $cmd->getDecryptionKeyName());
        $this->assertEquals('{showvar: $FOO decrypt=""}', $cmd->getNormalized());
    }

    public function test_decryptCustom() : void
    {
        $cmd = $this->getCommandFromString('{showvar: $FOO decrypt="custom-key"}');

        $this->assertTrue($cmd->isDecryptionEnabled());
        $this->assertEquals('custom-key', $cmd->getDecryptionKeyName());
        $this->assertEquals('{showvar: $FOO decrypt="custom-key"}', $cmd->getNormalized());
    }

    public function test_decryptMethods() : void
    {
        $cmd = $this->getCommandFromString('{showvar: $FOO}');

        $cmd->enableDecryption();

        $this->assertEquals('{showvar: $FOO decrypt="default"}', $cmd->getNormalized());

        $cmd->disableDecryption();

        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }

    public function test_decryptNoDefault() : void
    {
        $this->assertNull(DecryptSettings::getDefaultKeyName(), 'Precondition for the test is no default key.');

        $mailcode = $this->getCommandFromString('{showvar: $FOO decrypt=""}');

        $this->assertEmpty($mailcode->getDecryptionKeyName());
    }

    public function test_decryptDefaultKeyWhenDefault() : void
    {
        DecryptSettings::setDefaultKeyName('default-key');

        $cmd = $this->getCommandFromString('{showvar: $FOO decrypt=""}');

        $this->assertEquals('default-key', $cmd->getDecryptionKeyName());
    }

    public function test_decryptDefaultKeyWhenEmpty() : void
    {
        DecryptSettings::setDefaultKeyName('default-key');

        $cmd = $this->getCommandFromString('{showvar: $FOO decrypt=""}');

        $this->assertEquals('default-key', $cmd->getDecryptionKeyName());
    }

    private function getCommandFromString(string $command) : Mailcode_Commands_Command_ShowVariable
    {
        $cmd = Mailcode::create()->parseString($command)->getFirstCommand();
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $cmd);

        return $cmd;
    }
}
