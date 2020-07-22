<?php

use Mailcode\Mailcode_Commands_Command_ElseIf_BeginsWith;
use Mailcode\Mailcode_Factory;

final class Factory_ElseIfBeginsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf_BeginsWith::class;
    }
    
    public function test_elseIfBeginsWith()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::elseIfBeginsWith('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::elseIfBeginsWith('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::elseIfBeginsWith('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::elseIfBeginsWith('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
