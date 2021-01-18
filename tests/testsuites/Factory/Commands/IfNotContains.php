<?php

use Mailcode\Mailcode_Commands_Command_If_NotContains;
use Mailcode\Mailcode_Factory;

final class Factory_IfNotContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_NotContains::class;
    }
    
    public function test_ifContains()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::ifNotContains('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::ifNotContains('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::ifNotContains('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::ifNotContains('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
