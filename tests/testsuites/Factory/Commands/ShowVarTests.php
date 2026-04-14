<?php


declare(strict_types=1);

namespace MailcodeTests\Factory\Commands;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use MailcodeTestClasses\FactoryTestCase;

final class ShowVarTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ShowVariable::class;
    }
    
    public function test_showVar() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::show()->var('VAR.NAME'); }
        );

        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::show()->var('$VAR.NAME'); }
        );
    }
    
    public function test_showVar_error() : void
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::show()->var('0INVALIDVAR');
    }
}
