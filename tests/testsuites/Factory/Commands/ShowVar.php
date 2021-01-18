<?php

use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;

final class Factory_ShowVarTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ShowVariable::class;
    }
    
    public function test_showVar()
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
    
    public function test_showVar_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::show()->var('0INVALIDVAR');
    }
}
