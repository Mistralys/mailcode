<?php

declare(strict_types=1);

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Commands_Command_ShowPhone;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ShowPhoneTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showphone:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With variable, but without format',
                'string' => '{showphone: $FOO}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowPhone::VALIDATION_SOURCE_FORMAT_MISSING
            ),
            array(
                'label' => 'With variable, and empty format',
                'string' => '{showphone: $FOO ""}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowPhone::VALIDATION_INVALID_COUNTRY
            ),
            array(
                'label' => 'With variable, and invalid format',
                'string' => '{showphone: $FOO "BLA"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowPhone::VALIDATION_INVALID_COUNTRY
            ),
            array(
                'label' => 'With variable, and valid format',
                'string' => '{showphone: $FOO "DE"}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getVariable() : void
    {
        $cmd = Mailcode_Factory::show()->phone('PHONE', 'DE');

        $this->assertEquals('$PHONE', $cmd->getVariable()->getFullName());
    }

    public function test_getFormat() : void
    {
        $cmd = Mailcode_Factory::show()->phone('PHONE', 'de');

        $this->assertEquals('DE', $cmd->getSourceFormat());
    }

    public function test_urlEncoding() : void
    {
        $cmd = Mailcode_Factory::show()->phone('PHONE', 'DE', Mailcode_Factory::URL_ENCODING_ENCODE);

        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_urlDecoding() : void
    {
        $cmd = Mailcode_Factory::show()->phone('PHONE', 'DE', Mailcode_Factory::URL_ENCODING_DECODE);

        $this->assertTrue($cmd->isURLDecoded());
    }

    public function test_urlEncoding_commandString() : void
    {
        $cmd = Mailcode::create()->parseString('{showphone: $PHONE "DE" urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());
    }
}
