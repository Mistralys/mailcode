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

    public function test_urlencode()
    {
        $cmd = Mailcode::create()->parseString('{showvar: $FOO urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());
    }
}
