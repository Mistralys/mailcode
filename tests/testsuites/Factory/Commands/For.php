<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use Mailcode\Mailcode_Commands_Command_For;

final class Factory_ForTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_For::class;
    }
    
    public function test_for()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::for('SOURCE', 'LOOPVAR'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::for('$SOURCE', '$LOOPVAR'); }
        );
    }
    
    public function test_error_same_variable()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::for('$SOURCE', '$SOURCE');
    }
}
