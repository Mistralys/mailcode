<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ShowVarTests extends MailcodeTestCase
{
    public function test_validation()
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
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getVariable()
    {
        $cmd = Mailcode_Factory::showVar('foobar');
        
        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('$foobar', $cmd->getVariableName());
    }

    /**
     * URL encoding, when set, must automatically add the keyword
     * to the command. This way it is present when the command is normalized.
     */
    public function test_urlencode()
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());

        $cmd = Mailcode::create()->parseString('{showvar: $FOO}')->getFirstCommand();
        $cmd->setURLEncoding(true);

        $this->assertEquals('{showvar: $FOO urlencode:}', $cmd->getNormalized());

        $cmd->setURLEncoding(false);

        $this->assertEquals('{showvar: $FOO}', $cmd->getNormalized());
    }

    public function test_bothEncodings() : void
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO urlencode: urldecode:}');

        $this->assertFalse($cmd->isValid());
        $this->assertEquals(Mailcode_Commands_CommonConstants::VALIDATION_URL_DE_AND_ENCODE_ENABLED, $cmd->getFirstError()->getCode());
    }
}
