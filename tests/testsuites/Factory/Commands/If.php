<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_If;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If::class;
    }
    
    public function test_if() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::if()->if('$FOO.BAR == "Value"'); }
        );
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::if()->if('6 * 2 == 78'); }
        );
    }
}
