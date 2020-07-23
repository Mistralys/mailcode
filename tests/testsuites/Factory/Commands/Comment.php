<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_Command_Comment;

final class Factory_CommentTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_Comment::class;
    }
    
    public function test_if()
    {
        $this->runCommand(
            'Variable string comparison',
            function() { return Mailcode_Factory::comment('Just some text here'); }
        );
        
        $this->runCommand(
            'With quotes in the text',
            function() { return Mailcode_Factory::comment('Text with "a quoted part".'); }
        );
    }
}
