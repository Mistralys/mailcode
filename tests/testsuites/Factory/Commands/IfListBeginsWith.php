<?php

declare(strict_types=1);

use Mailcode\Mailcode_Commands_Command_If_ListBeginsWith;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_IfListBeginsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_ListBeginsWith::class;
    }
    
    public function test_ifContains() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::if()->listBeginsWith('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::if()->listBeginsWith('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::if()->listBeginsWith('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::if()->listBeginsWith('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
