<?php

use Mailcode\Mailcode_Commands_Command_If_SmallerThan;
use Mailcode\Mailcode_Factory;

final class Factory_IfSmallerThanTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_SmallerThan::class;
    }
    
    public function test_ifEmpty()
    {
        $this->runCommand(
            'String number, variable without dollar',
            function() { return Mailcode_Factory::if()->smallerThan('FOO.BAR', '45,12'); }
        );

        $this->runCommand(
            'String number, variable with dollar',
            function() { return Mailcode_Factory::if()->smallerThan('$FOO.BAR', '45,12'); }
        );
    }
}
