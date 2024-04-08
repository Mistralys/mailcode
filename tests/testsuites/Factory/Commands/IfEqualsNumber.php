<?php

use Mailcode\Mailcode_Commands_Command_If_EqualsNumber;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfEqualsNumberTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_EqualsNumber::class;
    }
    
    public function test_ifEmpty() : void
    {
        $this->runCommand(
            'String number, variable without dollar',
            function() { return Mailcode_Factory::if()->varEqualsNumber('FOO.BAR', '45,12'); }
        );

        $this->runCommand(
            'String number, variable with dollar',
            function() { return Mailcode_Factory::if()->varEqualsNumber('$FOO.BAR', '45,12'); }
        );
    }
}
