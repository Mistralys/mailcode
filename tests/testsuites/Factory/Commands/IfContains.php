<?php

use Mailcode\Mailcode_Commands_Command_If_Contains;
use Mailcode\Mailcode_Factory;

final class Factory_IfContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_Contains::class;
    }
    
    public function test_ifContains()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::ifContains('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::ifContains('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::ifContains('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::ifContains('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
