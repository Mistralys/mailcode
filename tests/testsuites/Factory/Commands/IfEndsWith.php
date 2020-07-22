<?php

use Mailcode\Mailcode_Commands_Command_If_EndsWith;
use Mailcode\Mailcode_Factory;

final class Factory_IfEndsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_EndsWith::class;
    }
    
    public function test_ifEndsWith()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::ifEndsWith('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::ifEndsWith('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::ifEndsWith('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::ifEndsWith('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
