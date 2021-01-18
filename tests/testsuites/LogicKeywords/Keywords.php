<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_LogicKeywords;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Commands_LogicKeywords_Keyword;

final class LogicKeywords_KeywordsTests extends MailcodeTestCase
{
    public function test_unsupported()
    {
        $cmd = Mailcode_Factory::misc()->end();
        
        $this->assertFalse($cmd->supportsLogicKeywords());
        
        $this->expectException(Mailcode_Exception::class);
        
        $cmd->getLogicKeywords();
    }
    
    public function test_supported() : void
    {
        $cmd = Mailcode_Factory::if()->ifEmpty('FOO.BAR');
        
        $this->assertTrue($cmd->supportsLogicKeywords());
        
        $logic = $cmd->getLogicKeywords();
        
        $this->assertInstanceOf(Mailcode_Commands_LogicKeywords::class, $logic);
        $this->assertFalse($logic->hasKeywords());
        $this->assertEmpty($logic->getKeywords());
    }
    
    public function test_append() : void
    {
        $cmd = Mailcode_Factory::if()->ifEmpty('FOO.BAR');
        
        $logic = $cmd->getLogicKeywords();

        $this->assertFalse($logic->hasKeywords());
        
        $keyword = $logic->appendAND('$FOO.BAR == "Value"', 'variable');
        
        $this->assertInstanceOf(Mailcode_Commands_LogicKeywords_Keyword::class, $keyword);
        
        $this->assertTrue($logic->hasKeywords());
    }
    
    public function test_append_noCommand() : void
    {
        $cmd = Mailcode_Factory::if()->ifEmpty('FOO.BAR');
        
        $logic = $cmd->getLogicKeywords();

        try
        {
            $logic->appendAND('nothing', 'variable');
        }
        catch(Mailcode_Exception $e)
        {
            $this->assertEquals(Mailcode_Commands_LogicKeywords::ERROR_CANNOT_APPEND_INVALID_KEYWORD, $e->getCode());
            
            // the details should contain the validation code
            $this->assertStringContainsString(Mailcode_Commands_LogicKeywords_Keyword::VALIDATION_NO_COMMAND_CREATED, $e->getDetails());
            return;
        }

        $this->fail('Should have triggered an exception.');
    }
    
    public function test_append_invalidCommand() : void
    {
        $cmd = Mailcode_Factory::if()->ifEmpty('FOO.BAR');
        
        $logic = $cmd->getLogicKeywords();
        
        try
        {
            $logic->appendAND('$FOOBAR = "John"', 'variable');
        }
        catch(Mailcode_Exception $e)
        {
            $this->assertEquals(Mailcode_Commands_LogicKeywords::ERROR_CANNOT_APPEND_INVALID_KEYWORD, $e->getCode());
            
            // the details should contain the validation code
            $this->assertStringContainsString(Mailcode_Commands_LogicKeywords_Keyword::VALIDATION_NO_COMMAND_CREATED, $e->getDetails());
            return;
        }
        
        $this->fail('Should have triggered an exception.');
    }
    
    public function test_normalize() : void
    {
        $cmd = Mailcode_Factory::if()->ifEmpty('FOO.BAR');
        
        $logic = $cmd->getLogicKeywords();
        $logic->appendAND('$FOO.BAR == "Argh"', 'variable');
        
        $expected = '{if empty: $FOO.BAR and variable: $FOO.BAR == "Argh"}';
        
        $this->assertEquals($expected, $cmd->getNormalized());
    }
    
    public function test_parseString() : void
    {
        $tests = array(
            array(
                'label' => 'Mixing keywords',
                'string' => '{if empty: $BARFOO and empty: $FOO.BAR or empty: $LOPOS}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_LogicKeywords::VALIDATION_CANNOT_MIX_LOGIC_KEYWORDS
            ),
            array(
                'label' => 'Invalid subcommand',
                'string' => '{if empty: $BARFOO and variable: invalid}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_LogicKeywords::VALIDATION_INVALID_SUB_COMMAND
            ),
            array(
                'label' => 'Concatenating several keywords',
                'string' => '{if empty: $BARFOO and not-empty: $FOO.BAR and: 1+1=2 and variable: $FOO.BAR == "Lopos"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $label = $test['label'].PHP_EOL;
            
            if(!$collection->isValid())
            {
                $label .= "Messages:".PHP_EOL;
                
                foreach($collection->getErrors() as $error)
                {
                    $label .= $error->getMessage().PHP_EOL;
                }
            }
            
            $label .= 'Command:'.$test['string'];
            
            $this->assertSame($test['valid'], $collection->isValid(), $label);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $label);
            }
        }
    }
}
