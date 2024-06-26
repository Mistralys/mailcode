<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Commands_Command;

final class Mailcode_CommandsTests extends MailcodeTestCase
{
    public function test_createParser() : void
    {
        $commands = Mailcode::create()->getCommands();
        
        $this->assertInstanceOf(Mailcode_Commands::class, $commands);
    }
    
    public function test_getCommands() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $commands = $collection->getAll(); 
        
        $this->assertNotEmpty($commands);
    }
    
    public function test_getCommandByID() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $command = $collection->getByID('ShowVariable');
        
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $command);
        $this->assertEquals('ShowVariable', $command->getID());
        $this->assertEquals('showvar', $command->getName());
    }

    public function test_isDummy() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $dummy = $collection->getByID('ShowVariable');
        
        $this->assertTrue($dummy->isDummy(), 'The informational instances should say they are dummies.');
        
        $real = $collection->createCommand('ShowVariable', '', 'CUSTOMER.CONTRACT_ID', '{showvar:CUSTOMER.CONTRACT_ID}');
        
        $this->assertFalse($real->isDummy());
    }

    public function test_hash() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $text = '{showvar:CUSTOMER.CONTRACT_ID}';
        $hash = md5($text);
        
        $real = $collection->createCommand(
            'ShowVariable', 
            '', 
            'CUSTOMER.CONTRACT_ID', 
            '{showvar:CUSTOMER.CONTRACT_ID}'
        );
        
        $this->assertEquals($hash, $real->getHash());
    }

    public function test_hashDummy() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $dummy = $collection->getByID('ShowVariable');
        
        $this->expectException(Mailcode_Exception::class);
        
        $dummy->getHash();
    }
    
    public function test_nameExists() : void
    {
        $collection = Mailcode::create()->getCommands();

        $this->assertTrue($collection->nameExists('showvar'));
        
        $this->assertFalse($collection->nameExists('foo'));
    }
    
    public function test_idExists() : void
    {
        $collection = Mailcode::create()->getCommands();
        
        $this->assertTrue($collection->idExists('ShowVariable'));
        
        $this->assertFalse($collection->idExists('foo'));
    }
    
    public function test_normalize() : void
    {
        $tests = array(
            array(
                'label' => 'Showvar',
                'string' => '  {  showvar  : $FOO.BAR  }  ',
                'expected' => '{showvar: $FOO.BAR}'
            ),
            array(
                'label' => 'Else ',
                'string' => '  {  else  }  ',
                'expected' => '{else}'
            ),
            array(
                'label' => 'Setvar with newlines in the parameters',
                'string' => '  
                {  
                    setvar: 
                    $FOO.BAR
                    = 
                    "Text"  
                }  ',
                'expected' => '{setvar: $FOO.BAR = "Text"}'
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            $command = $collection->getFirstCommand();

            $this->assertNotNull($command);
            $this->assertSame($test['expected'], $command->getNormalized());
        }
    }
}
