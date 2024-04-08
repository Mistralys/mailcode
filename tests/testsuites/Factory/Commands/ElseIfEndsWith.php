<?php

use Mailcode\Mailcode_Commands_Command_ElseIf_EndsWith;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ElseIfEndsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf_EndsWith::class;
    }
    
    public function test_elseIfEndsWith() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::elseIf()->endsWith('FOO.BAR', 'Value'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::elseIf()->endsWith('$VAR.NAME', 'Value'); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::elseIf()->endsWith('$VAR.NAME', '64'); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::elseIf()->endsWith('$VAR.NAME', 'It\'s a "weird" foo.'); }
        );
    }
}
