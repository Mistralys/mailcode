<?php

declare(strict_types=1);

use Mailcode\Mailcode_Commands_Command_Else;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ElseTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_Else::class;
    }
    
    public function test_else() : void
    {
        $this->runCommand(
            'Creating the command',
            function() { return Mailcode_Factory::elseIf()->else(); }
        );
    }
}
