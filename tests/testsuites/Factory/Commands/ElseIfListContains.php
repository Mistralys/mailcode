<?php

use Mailcode\Mailcode_Commands_Command_ElseIf_ListContains;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ElseIfListContainsTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf_ListContains::class;
    }
    
    public function test_elseIfContains() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::elseIf()->listContains('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::elseIf()->listContains('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::elseIf()->listContains('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::elseIf()->listContains('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
