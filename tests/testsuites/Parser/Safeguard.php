<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Parser_SafeguardTests extends MailcodeTestCase
{
   /**
    * Test safeguarding with default settings.
    */
    public function test_safeguard()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with a {showvar: $VAR.NAME} variable.';
        
        $safeguard = $parser->createSafeguard($original);
        
        $text = $safeguard->makeSafe();
        
        $this->assertStringContainsString($safeguard->getDelimiter(), $text);
        
        // do something destructive that would usually break the command
        $text = str_replace('{', 'BRACE', $text);
        
        $this->assertNotEquals($original, $text);
        
        $result = $safeguard->makeWhole($text);
        
        $this->assertEquals($original, $result);
    }
    
   /**
    * Checks that the safeguarding is indeed case neutral.
    */
    public function test_safeguard_caseNeutral()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with a {showvar: $VAR.NAME} VARIABLE.';
        
        $safeguard = $parser->createSafeguard($original);
        
        $text = $safeguard->makeSafe();
        
        $text = strtolower($text);
        
        $result = $safeguard->makeWhole($text);
        
        $this->assertEquals('text with a {showvar: $VAR.NAME} variable.', $result);
    }
    
   /**
    * Test that trying to safeguard a string containing
    * broken commands triggers an exception, and shows
    * that it is not valid.
    */
    public function test_safeguard_brokenCommand()
    {
        $parser = Mailcode::create()->getParser();
       
        $original = 'Text with an unknown {unknowncommand} command.';
        
        $safeguard = $parser->createSafeguard($original);
        
        $this->assertFalse($safeguard->isValid());
        
        $this->expectException(Mailcode_Exception::class);
        
        $safeguard->makeSafe();
    }
    
   /**
    * Ensures that calling makeWhole() with broken or missing
    * placeholders will trigger an exception.
    */
    public function safeguard_brokenPlaceholders()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with a {showvar: $VAR.NAME} variable.';
        
        $safeguard = $parser->createSafeguard($original);
        
        $text = $safeguard->makeSafe();
        
        // break the placeholders by removing the delimiters 
        $text = str_replace($safeguard->getDelimiter(), '', $text);

        $this->expectException(Mailcode_Exception::class);
        
        $safeguard->makeWhole($text);
    }
    
   /**
    * Test changing the placeholder delimiter characters.
    */
    public function test_setDelimiter()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with a {showvar: $VAR.NAME} variable.';
        
        $safeguard = $parser->createSafeguard($original);
        $safeguard->setDelimiter('$');
        
        $text = $safeguard->makeSafe();
        
        $this->assertStringContainsString($safeguard->getDelimiter(), $text);
        
        // do something destructive that would break the command with the standard delimiter
        $text = str_replace('_', 'UNDERSCORE', $text);
        
        $this->assertNotEquals($original, $text);
        
        $result = $safeguard->makeWhole($text);
        
        $this->assertEquals($original, $result);
    }
    
   /**
    * Ensure that it is not possible to use empty delimiters.
    */
    public function test_setDelimiter_empty()
    {
        $parser = Mailcode::create()->getParser();
        
        $safeguard = $parser->createSafeguard('');
        
        $this->expectException(Mailcode_Exception::class);
        
        $safeguard->setDelimiter('');
    }
    
    public function test_makeSafe_separateLines()
    {
        $tests = array(
            array(
                'label' => 'Within a sentence, without newlines',
                'text' => 'Text with a {setvar: $FOOBAR = "Value"} variable.',
                'expected' =>  
'Text with a 
{setvar: $FOOBAR = "Value"}
 variable.'
            ),
            array(
                'label' => 'At the start of the string, with text appended',
                'text' => '{setvar: $FOOBAR = "Value"} and some text.',
                'expected' => 
'{setvar: $FOOBAR = "Value"}
 and some text.'
            ),
            array(
                'label' => 'At the end of the string.',
                'text' => 'Some text and {setvar: $FOOBAR = "Value"}',
                'expected' => 
'Some text and 
{setvar: $FOOBAR = "Value"}'
            ),
            array(
                'label' => 'On its own line, but with text appended',
                'text' => 
'Some text here.
{setvar: $FOOBAR = "Value"} also here.',
                'expected' => 
'Some text here.
{setvar: $FOOBAR = "Value"}
 also here.'            
            ),
            array(
                'label' => 'No newlines needed',
                'text' => '{setvar: $FOOBAR = "Value"}',
                'expected' => '{setvar: $FOOBAR = "Value"}'
            ),
            array(
                'label' => 'Single char before and after (shorter than the EOL character)',
                'text' => '-{setvar: $FOOBAR = "Value"}-',
                'expected' => 
'-
{setvar: $FOOBAR = "Value"}
-'
            ),
            array(
                'label' => 'Unicode chars in the command (potential string length issues)',
                'text' => '{if variable: $FOOBAR == "öäü"}Text here{end}',
                'expected' =>
                '{if variable: $FOOBAR == "öäü"}
Text here
{end}'
            ),
            array(
                'label' => 'Show variable commands should not be modified (they generate content)',
                'text' => 'This is {showvar: $FOOBAR} a regular variable.',
                'expected' => 'This is {showvar: $FOOBAR} a regular variable.'
            )
        );
        
        $parser = Mailcode::create()->getParser();

        foreach($tests as $test)
        {
            $safeguard = $parser->createSafeguard($test['text']);
            $safeguard->selectSingleLinesFormatter();
            
            $safe = $safeguard->makeSafe();
            $result = $safeguard->makeWhole($safe);
            
            $this->assertEquals($test['expected'], $result, $test['label']);
        }
    }
}
