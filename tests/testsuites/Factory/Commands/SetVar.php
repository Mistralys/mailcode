<?php

use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;

final class Factory_SetVarTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_SetVariable::class;
    }
    
    public function test_setVar()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::setVar('VAR.NAME', 'Some text'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::setVar('$VAR.NAME', 'Some text'); }
        );
        
        $this->runCommand(
            'Unquoted params',
            function() { return Mailcode_Factory::setVar('$VAR.NAME', '6 + 2', false); }
        );
    }
    
    public function test_setVar_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::setVar('$FOO.BAR', 'Some text', false);
    }
}
