<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_If_BiggerThan;

final class Factory_IfBiggerThanTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_If_BiggerThan::class;
    }
    
    public function test_ifEmpty()
    {
        $this->runCommand(
            'String number, variable without dollar',
            function() { return Mailcode_Factory::ifBiggerThan('FOO.BAR', '45,12'); }
        );

        $this->runCommand(
            'String number, variable with dollar',
            function() { return Mailcode_Factory::ifBiggerThan('$FOO.BAR', '45,12'); }
        );
    }
}
