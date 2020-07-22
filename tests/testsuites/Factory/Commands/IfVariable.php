<?php

use Mailcode\Mailcode_Commands_Command_If_Variable;
use Mailcode\Mailcode_Factory;

final class Factory_IfVariableTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_Variable::class;
    }
    
    public function test_ifVariable()
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::ifVar('FOO.BAR', '==', 'Some text', true); }
        );
        
        $this->runCommand(
            'Arithmetic operation, greater than',
            function() { return Mailcode_Factory::ifVar('$FOO.BAR', '>', '6 * 2'); }
        );
        
        $this->runCommand(
            'Arithmetic operation, smaller than',
            function() { return Mailcode_Factory::ifVar('$FOO.BAR', '<', '14.56'); }
        );
    }
    
    public function test_ifVariableEquals()
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::ifVarEquals('FOO.BAR', 'Some text', true); }
        );
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::ifVarEquals('$FOO.BAR', '6 * 2'); }
        );
    }
}
