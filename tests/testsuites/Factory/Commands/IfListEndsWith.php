<?php

declare(strict_types=1);

use Mailcode\Mailcode_Commands_Command_If_ListEndsWith;
use Mailcode\Mailcode_Factory;

final class Factory_IfListEndsWithTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_ListEndsWith::class;
    }
    
    public function test_ifContains()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::if()->listEndsWith('FOO.BAR', array('Value')); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::if()->listEndsWith('$VAR.NAME', array('Value')); }
        );
        
        $this->runCommand(
            'Search for number',
            function() { return Mailcode_Factory::if()->listEndsWith('$VAR.NAME', array('64')); }
        );
        
        $this->runCommand(
            'Search for text with quotes',
            function() { return Mailcode_Factory::if()->listEndsWith('$VAR.NAME', array('It\'s a "weird" foo.')); }
        );
    }
}
