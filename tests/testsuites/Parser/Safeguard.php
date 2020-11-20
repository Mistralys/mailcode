<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;

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
    public function test_caseNeutral()
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
    public function test_brokenCommand()
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
    public function test_brokenPlaceholders()
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
    * Ensures that calling makeWholePartial() will ignore
    * missing placeholders.
    */
    public function test_makeWholePartial()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with {showvar: $VAR.NAME}_SPLIT_{showvar: $FOO.BAR} variables.';
        
        $safeguard = $parser->createSafeguard($original);
        $safeguard->makeSafe();
        
        $parts = explode('_SPLIT_', $original);
        
        $whole = $safeguard->makeWholePartial(array_pop($parts));
        
        $this->assertEquals('{showvar: $FOO.BAR} variables.', $whole);
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
    
    public function test_makeSafe_invalidCollection()
    {
        $parser = Mailcode::create()->getParser();
       
        $safeguard = $parser->createSafeguard('{if variable: $FOOBAR == "true"}');

        $this->expectException(Mailcode_Exception::class);
        
        $safeguard->makeSafe();
    }
    
    public function test_makeSafePartial()
    {
        $parser = Mailcode::create()->getParser();
        
        $safeguard = $parser->createSafeguard('{if variable: $FOOBAR == "true"}');
        
        $safeguard->makeSafePartial();
        
        // no exception = success
        $this->addToAssertionCount(1);
    }
    
   /**
    * Ensure that the safeguarded string correctly uses the 
    * normalized variants of the commands, not the original
    * matched strings.
    */
    public function test_normalize()
    {
        $parser = Mailcode::create()->getParser();
        
        $original = 'Text with a {showvar:        $VAR.NAME            } VARIABLE.';
        
        $safeguard = $parser->createSafeguard($original);
        
        $text = $safeguard->makeSafe();
        
        try
        {
            $result = $safeguard->makeWhole($text);
        }
        catch(Mailcode_Exception $e)
        {
            $this->fail(
                'Exception #'.$e->getCode().': '.$e->getMessage().PHP_EOL.$e->getDetails().PHP_EOL.
                $e->getTraceAsString()
            );
        }
        
        $this->assertEquals('Text with a {showvar: $VAR.NAME} VARIABLE.', $result);
    }

    /**
     * Trying to enable URL encoding on a command that does
     * not support it must trigger an exception.
     */
    public function test_url_encoding_unsupported() : void
    {
        $command = Mailcode_Factory::ifVarEqualsString('FOO', '==', 'Test');

        try{
            $command->setURLEncoding(true);
        }
        catch(Mailcode_Exception $e)
        {
            $this->assertEquals(
                Mailcode_Commands_Command::ERROR_URL_ENCODING_NOT_SUPPORTED,
                $e->getCode()
            );

            return;
        }

        $this->fail('Should have triggered the expected exception.');
    }

    /**
     * When the safeguard detects commands in URLs, the URL encoding
     * must be automatically turned on, except in Email addresses, which
     * should be ignored.
     */
    public function test_auto_url_encoding() : void
    {
        $original =
            'Lorem ipsum dolor http://google.com?var={showvar: $FOO} sit amet. '.
            'Ipsum lorem mailto:{showvar: $BAR} dolor amet.';

        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($original);

        // Need to call this to trigger the automatic URL search
        $safeguard->makeSafe();

        $placeholders = $safeguard->getPlaceholders();

        $this->assertCount(2, $placeholders);

        $this->assertTrue($placeholders[0]->getCommand()->isURLEncoded());
        $this->assertFalse($placeholders[1]->getCommand()->isURLEncoded());
    }

    /**
     * When using commands in an URL that do not support URL
     * encoding, they must be ignored to avoid triggering an
     * exception.
     *
     * @see Mailcode_Parser_Safeguard::analyzeURLs()
     */
    public function test_auto_url_encoding_notSupported() : void
    {
        $parser = Mailcode::create()->getParser();

        $original = 'Lorem ipsum dolor https://google.com/{setvar: $BAR "Value"}/path sit amet.';

        $safeguard = $parser->createSafeguard($original);

        // This would trigger an exception if the safeguard
        // tried to enable URL encoding on a non url encode-able
        // command.
        $safeguard->makeSafe();

        $this->addToAssertionCount(1);
    }

    public function test_auto_url_encoding_ignoreIfDecoded() : void
    {
        $original = 'Lorem ipsum dolor http://google.com?var={showvar: $FOO urldecode:} sit amet.';

        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($original);

        // Need to call this to trigger the automatic URL search
        $safeguard->makeSafe();

        $placeholders = $safeguard->getPlaceholders();

        $this->assertCount(1, $placeholders);

        $this->assertFalse($placeholders[0]->getCommand()->isURLEncoded());
    }
}
