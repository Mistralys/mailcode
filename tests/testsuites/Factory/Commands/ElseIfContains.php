<?php

use Mailcode\Mailcode_Commands_Command_ElseIf_Contains;
use Mailcode\Mailcode_Factory;

final class Factory_ElseIfContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf_Contains::class;
    }
    
    public function test_elseIfContains()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::elseIf()->elseIfContains('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::elseIf()->elseIfContains('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::elseIf()->elseIfContains('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::elseIf()->elseIfContains('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
