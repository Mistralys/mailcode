<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_ElseIf;

final class Factory_ElseIfTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf::class;
    }
    
    public function test_elseIf()
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::elseIf('$FOO.BAR == "Value"'); }
        );
        
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::elseIf('6 * 2 == 78'); }
        );
    }
}
