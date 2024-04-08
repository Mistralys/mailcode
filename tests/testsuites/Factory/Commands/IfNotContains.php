<?php

use Mailcode\Mailcode_Commands_Command_If_NotContains;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfNotContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_NotContains::class;
    }
    
    public function test_ifContains() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::if()->notContains('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::if()->notContains('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::if()->notContains('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::if()->notContains('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
