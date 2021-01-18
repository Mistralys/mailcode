<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_If_Empty;

final class Factory_IfEmptyTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_Empty::class;
    }
    
    public function test_ifEmpty()
    {
        $this->runCommand(
            'Variable without dollar sign',
            function() { return Mailcode_Factory::if()->empty('FOO.BAR'); }
        );
        
        $this->runCommand(
            'Variable with dollar sign',
            function() { return Mailcode_Factory::if()->empty('$FOO.BAR'); }
        );
    }
}
