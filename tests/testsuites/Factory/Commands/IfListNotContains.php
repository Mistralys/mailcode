<?php

use Mailcode\Mailcode_Commands_Command_If_ListNotContains;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfListNotContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_ListNotContains::class;
    }
    
    public function test_ifContains() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::if()->listNotContains('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::if()->listNotContains('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::if()->listNotContains('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::if()->listNotContains('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
