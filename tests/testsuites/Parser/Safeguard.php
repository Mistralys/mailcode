<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Parser_Safeguard;
use Mailcode\Mailcode_Parser_Safeguard_Formatter_Location;
use function AppUtils\parseURL;

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
        $safeguard->setDelimiter('222');
        
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
        $command = Mailcode_Factory::if()->varEqualsString('FOO', 'Test');

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

    public function test_auto_url_encoding_ignorePhoneURLs() : void
    {
        $original = 'Lorem ipsum dolor tel://{showvar: $FOO} sit amet.';

        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($original);

        // Need to call this to trigger the automatic URL search
        $safeguard->makeSafe();

        $placeholders = $safeguard->getPlaceholders();

        $this->assertCount(1, $placeholders);

        $this->assertFalse($placeholders[0]->getCommand()->isURLEncoded());
    }

    /**
     * Test for a very specific bug. Placeholders used to be
     * zero-padded, which caused issues in the case where the
     * first and tenth commands would get the same placeholder
     * string if they had the same length, because both placeholders
     * would look like this: "___1000000000000___".
     *
     * This is why placeholders were modified to use an ID
     * separator character, like this: "___10*000000000___".
     */
    public function test_placeholder_duplicates() : void
    {
        $markup = <<<'EOD'
01. {showvar: $VAR.ABC}
02. {showvar: $VAR.ABCD}
03. {showvar: $VAR.ABCDE}
04. {showvar: $VAR.ABCDEF}
05. {showvar: $VAR.ABCDEFG}
06. {showvar: $VAR.ABCDEFGH}
07. {showvar: $VAR.ABCDEFGHI}
08. {showvar: $VAR.ABCDEFGHIJ}
09. {showvar: $VAR.ABCDEFGHIJK}
10. {showvar: $VAR.CBA} (same length as command Nr 1)
EOD;

        Mailcode_Parser_Safeguard::resetCounter();

        $safeguard = Mailcode::create()->createSafeguard($markup);
        $placeholders = $safeguard->getPlaceholders();
        $stack = array();

        foreach($placeholders as $placeholder)
        {
            $replacement = $placeholder->getReplacementText();

            $this->assertNotContains($replacement, $stack, 'Found a duplicate placeholder!');

            $stack[] = $replacement;
        }
    }

    public function test_invalidDelimiter() : void
    {
        $delimiters = array(
            '_' => false, // min length = 2
            '_*_' => false, // not urlencode independent
            '__1' => true, // number at the end
            '1__' => true, // number at the beginning
            '999' => true,
            '_1_' => true,
            'AAA' => true,
            'abc' => true,
        );

        foreach($delimiters as $delimiter => $valid)
        {
            $safeguard = Mailcode::create()->createSafeguard('');

            try
            {
                $safeguard->setDelimiter($delimiter);
            }
            catch(Mailcode_Exception $e)
            {
                if($valid)
                {
                    $this->fail('Delimiter ['.$delimiter.'] should be valid.');
                }
                else
                {
                    $this->addToAssertionCount(1);
                }
                continue;
            }

            if(!$valid)
            {
                $this->fail('Delimiter ['.$delimiter.'] should be invalid.');
            }
            else
            {
                $this->addToAssertionCount(1);
            }
        }
    }

    /**
     * Ensure that an exception is thrown when trying to
     * restore a string using the strict, non-partial
     * replacement method.
     */
    public function test_placeholderNotFound() : void
    {
        $subject = 'Here is some {showvar: $FOO} text.';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $safeguard->makeSafe();

        // Use a text in which the placeholder is missing
        $safe = 'Here is some text.';

        try
        {
            $safeguard->makeWhole($safe);
        }
        catch (Mailcode_Exception $e)
        {
            $this->assertEquals(
                Mailcode_Parser_Safeguard_Formatter_Location::ERROR_PLACEHOLDER_NOT_FOUND,
                $e->getCode()
            );

            $this->assertStringContainsString('{showvar: $FOO}', $e->getDetails());

            return;
        }

        $this->fail('No exception has been triggered.');
    }

    /**
     * URL encoding the subject string once safeguarded must not break
     * the placeholders.
     *
     * @throws Mailcode_Exception
     */
    public function test_makeSafe_URLEncodingPlaceholders() : void
    {
        $subject = 'Here is some {showvar: $FOO} text.';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $safe = $safeguard->makeSafe();

        $safe = urlencode($safe);

        $whole = urldecode($safeguard->makeWhole($safe));

        $this->assertEquals($subject, $whole);
    }

    /**
     * The safeguard placeholders were using hyphens at some point,
     * to separate the placeholder ID from the zero-padding. This
     * caused issues when highlighting URLs with commands, because
     * the highlighter replaces hyphens with "-<wbr>" to improve
     * word wrapping.
     */
    public function test_highlightURL() : void
    {
        $url = 'https://domain.com/path/?param={showvar: $FOO}&bar=lopos';

        $safeguard = Mailcode::create()->createSafeguard($url);

        $safe = $safeguard->makeSafe();

        $safe = parseURL($safe)->getHighlighted();

        $safeguard->makeWhole($safe);

        // No exception = safeguarding works.
        $this->addToAssertionCount(1);
    }

    /**
     * Test for a bug: Because the safeguard used a non-strict
     * in_array comparison to check if a placeholder exists,
     * the string "999000001999." (note the dot at the end) would
     * match the placeholder "999000001999".
     *
     * In essence, this comparison is true in PHP:
     * <code>"45." == "45"</code>
     */
    public function test_isPlaceholder_bugNumeric() : void
    {
        $text = '{showvar: $FOO}';

        $safeguard = Mailcode::create()->createSafeguard($text);

        $placeholders = $safeguard->getPlaceholderStrings();

        foreach($placeholders as $placeholder)
        {
            if($safeguard->isPlaceholder($placeholder.'.'))
            {
                $this->fail('Should not find a placeholder.');
            }
        }

        $this->addToAssertionCount(1);
    }
}
