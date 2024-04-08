<?php

use Mailcode\Mailcode_Commands_Command_If_Variable;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfVariableTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_Variable::class;
    }
    
    public function test_ifVariable() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::if()->var('FOO.BAR', '==', 'Some text', true); }
        );
        
        $this->runCommand(
            'Arithmetic operation, greater than',
            function() { return Mailcode_Factory::if()->var('$FOO.BAR', '>', '6 * 2'); }
        );
        
        $this->runCommand(
            'Arithmetic operation, smaller than',
            function() { return Mailcode_Factory::if()->var('$FOO.BAR', '<', '14.56'); }
        );
    }
    
    public function test_ifVariableEquals() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::if()->varEquals('FOO.BAR', 'Some text', true); }
        );
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::if()->varEquals('$FOO.BAR', '6 * 2'); }
        );
    }

    public function test_ifVariableEqualsString() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'false', true); }
        );

        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::if()->varEqualsString('$FOO.BAR', 'false', true); }
        );
    }
}
