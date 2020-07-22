<?php

use Mailcode\Mailcode_Commands_Command_If_BeginsWith;
use Mailcode\Mailcode_Factory;

final class Factory_IfBeginsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_BeginsWith::class;
    }
    
    public function test_ifBeginsWith()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::ifBeginsWith('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::ifBeginsWith('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::ifBeginsWith('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::ifBeginsWith('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
