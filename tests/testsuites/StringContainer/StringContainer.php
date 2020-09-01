<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_StringContainer;

final class StringContainer_StringContainerTests extends MailcodeTestCase
{
    public function test_createString() : void
    {
        $subject = Mailcode::create()->createString('Test');
        
        $this->assertInstanceOf(Mailcode_StringContainer::class, $subject);
    }

    public function test_stringMatches() : void
    {
        $subject = Mailcode::create()->createString('Test');
        
        $this->assertSame('Test', $subject->getString());
    }
    
    public function test_idIncrement() : void
    {
        $subject1 = Mailcode::create()->createString('Test1');
        $subject2 = Mailcode::create()->createString('Test2');
        
        $this->assertTrue($subject1->getID() !== $subject2->getID());
    }

    public function test_length() : void
    {
        $string = 'Some test text with öäü special characters';
        
        $subject = Mailcode::create()->createString($string);
        
        $this->assertEquals(mb_strlen($string), $subject->getLength());
    }

    public function test_updateString() : void
    {
        $string = 'Some test text with öäü special characters';
        
        $subject = Mailcode::create()->createString('Test');
        
        $subject->updateString($string);
        
        $this->assertEquals($string, $subject->getString());
        $this->assertEquals(mb_strlen($string), $subject->getLength());
    }
    
    public function test_listener() : void
    {
        $subject = Mailcode::create()->createString('Test');
        
        $subject->addListener(array($this, 'handle_listenerCallback'));
        
        $subject->updateString('New test');
        
        $this->assertSame($subject->getID(), $this->calledID);
    }
    
    public function test_removeListener() : void
    {
        $subject = Mailcode::create()->createString('Test');
        
        $id = $subject->addListener(array($this, 'handle_listenerCallback'));
        $subject->removeListener($id);
        
        $subject->updateString('New test');
        
        $this->assertNotEquals($subject->getID(), $this->calledID);
    }
    
   /**
    * @var integer
    */
    private $calledID = -1;
    
    public function handle_listenerCallback(Mailcode_StringContainer $subject) : void
    {
        $this->calledID = $subject->getID();
    }
}
