<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use Mailcode\Mailcode_Commands_Command_ShowSnippet;

final class Factory_ShowSnippetTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ShowSnippet::class;
    }
    
    public function test_showSnippet()
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::show()->snippet('snippet_name'); }
        );
        
        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::show()->snippet('$snippet_name'); }
        );
    }
    
    public function test_showSnippet_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::show()->snippet('0invalid_var');
    }
}
