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
            function() { return Mailcode_Factory::elseIf()->contains('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::elseIf()->contains('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::elseIf()->contains('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::elseIf()->contains('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
