<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_ElseIf_Variable;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ElseIfVariableTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf_Variable::class;
    }
    
    public function test_elseIfVariable() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::elseIf()->var('FOO.BAR', '==', 'Some text', true); }
        );
        $this->runCommand(
            'Arithmetic operation, greater than',
            function() { return Mailcode_Factory::elseIf()->var('$FOO.BAR', '>', '6 * 2'); }
        );
        $this->runCommand(
            'Arithmetic operation, smaller than',
            function() { return Mailcode_Factory::elseIf()->var('$FOO.BAR', '<', '14.56'); }
        );
    }
    
    public function test_elseIfVariableEquals() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::elseIf()->varEquals('FOO.BAR', 'Some text', true); }
        );
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::elseIf()->varEquals('$FOO.BAR', '6 * 2'); }
        );
    }
}
