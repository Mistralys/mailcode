<?php

declare(strict_types=1);

use Mailcode\Mailcode_Commands_Command_Break;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_BreakTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_Break::class;
    }
    
    public function test_break() : void
    {
        $this->runCommand(
            'Creating the command',
            function() { return Mailcode_Factory::misc()->break(); }
        );
    }
}
