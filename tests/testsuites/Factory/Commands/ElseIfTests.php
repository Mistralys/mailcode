<?php


declare(strict_types=1);

namespace MailcodeTests\Factory\Commands;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_ElseIf;
use MailcodeTestClasses\FactoryTestCase;

final class ElseIfTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ElseIf::class;
    }
    
    public function test_elseIf() : void
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::elseIf()->elseIf('$FOO.BAR == "Value"'); }
        );
        
        
        $this->runCommand(
            'Arithmetic operation',
            function() { return Mailcode_Factory::elseIf()->elseIf('6 * 2 == 78'); }
        );
    }
}
