<?php

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ShowNumberTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ShowNumber::class;
    }
    
    public function test_showNumber() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::show()->number('VAR.NAME'); }
        );

        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::show()->number('$VAR.NAME', '1000'); }
        );
    }
    
    public function test_showNumber_error() : void
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::show()->number('0INVALIDVAR');
    }
}
