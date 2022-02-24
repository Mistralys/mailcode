<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection_Error_Command;
use Mailcode\Mailcode_Collection_Error_Message;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Parser;
use Mailcode\Mailcode_Parser_StringPreProcessor;

final class Parser_ParserTests extends MailcodeTestCase
{
    public function test_createParser()
    {
        $parser = Mailcode::create()->getParser();
        
        $this->assertInstanceOf(Mailcode_Parser::class, $parser);
    }
    
    public function test_parseString_withoutCommands()
    {
        $collection = Mailcode::create()->parseString("Some text without commands");
        
        $this->assertSame(0, $collection->countCommands());
        $this->assertFalse($collection->hasCommands());
    }

    public function test_parseString_withSingleCommand()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: \$CUSTOMER.NAME}"
        );
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
    }
    
    public function test_parseString_caseInsensitive()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {ShowVar: \$CUSTOMER.NAME}"
        );
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
    }
    
    public function test_parseString_freeSpacing()
    {
        $collection = Mailcode::create()->parseString(
            "Line with 

            {
                \tshowvar: 

                \$CUSTOMER

        .
                NAME

                }"
        );
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
    }
    
   /**
    * WYSIWYG editors will enforce spaces the user adds by adding
    * non breaking space entities. These should be filtered out,
    * by replacing them with actual spaces. This way, these entities
    * will be preserved when used in string literals, but stripped
    * out otherwise. 
    */
    public function test_parseString_htmlSpaces()
    {
        $collection = Mailcode::create()->parseString(
            "Line with
            
            {if variable: &#160; \$CUSTOMER.NAME &nbsp; == \" &nbsp; &#160; \" }

            "
        );
        
        $expected = '{if variable: $CUSTOMER.NAME == "     "}';
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
        
        $cmd = $collection->getFirstCommand();
        $this->assertEquals($expected, $cmd->getNormalized());
    }

    public function test_parseString_withSeveralCommands()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: \$CUSTOMER.NAME}
            and another one further {showvar: \$ADDRESS.LINE1} down."
        );
        
        $this->assertSame(2, $collection->countCommands());
    }

    public function test_parseString_withDuplicateCommands()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: \$CUSTOMER.NAME}
            and another one further {showvar: \$ADDRESS.LINE1} down.
            The third line has a duplicate: {showvar: \$CUSTOMER.NAME}.
            The fourth is the same, but not a duplicate: {   showvar  : \$CUSTOMER.NAME}"
        );
        
        $hashed = $collection->getGroupedByHash();
        
        $this->assertSame(4, $collection->countCommands());
        $this->assertSame(3, count($hashed));
    }
    
    public function test_parseString_unknownCommandName()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {foo: \$CUSTOMER.NAME} {showvar: \$ADDRESS.LINE1}"
        );
        
        $this->assertFalse($collection->isValid());
        $this->assertSame(1, $collection->countCommands());
        
        $errors = $collection->getErrors();
        $total = count($errors);
        
        $this->assertSame(1, $total);
        $this->assertInstanceof(Mailcode_Collection_Error_Message::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_UNKNOWN_COMMAND_NAME, $errors[0]->getCode());
        $this->assertTrue($errors[0]->isUnknownCommand());
    }
    
    public function test_parseString_typeNotSupported()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar foo: \$CUSTOMER.NAME}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_ADDONS_NOT_SUPPORTED, $errors[0]->getCode());
        $this->assertTrue($errors[0]->isTypeNotSupported());
    }
    
    public function test_parseString_invalidVariableName()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: \$FOO.8AR}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT, $errors[0]->getCode());
    }

    public function test_parseString_invalidVariablePath()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: \$3OO.BAR}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT, $errors[0]->getCode());
    }
    
    public function test_parseString_noVariable()
    {
        $collection = Mailcode::create()->parseString(
            "Line with {showvar: FOO.BAR}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT, $errors[0]->getCode());
    }
    
    public function test_parseHTML()
    {
        $string = '
<p>We need some text here, apparently.</p>
<p>{showvar: $CUSTOMER.CUSTOMER_ID}</p>
<p>{if variable: $FOO.BAR == "NOPE"}</p>
<p>Some text here in the IF command.</p>
<p>{end}</p>
<p>
    <strong>Some text&nbsp;{showvar: $CUSTOMER.CUSTOMER_ID}</strong>
    <strong>&#8203; with variable</strong>
</p>
<p>And only a variable:</p>
<p><strong>{showvar: $CUSTOMER.CUSTOMER_ID}</strong></p>
';
        $safe = Mailcode::create()->createSafeguard($string);
        
        $subject = $safe->makeSafe();
        
        $safe->makeWhole($subject);
        
        $this->addToAssertionCount(1);
    }
    
    public function test_parseWithCSS()
    {
        $subject = 
'<style>
.classname{
width:50%;
height:45px;
}
</style>';
        
        $collection = Mailcode::create()->parseString($subject);
        
        $this->assertTrue($collection->isValid());
        $this->assertFalse($collection->hasCommands());
    }
    
    public function test_parseWithCSS_variant2()
    {
        $subject =
        '<style>#id{width:50%}</style>';
        
        $collection = Mailcode::create()->parseString($subject);
        
        $this->assertTrue($collection->isValid());
        $this->assertFalse($collection->hasCommands());
    }
    
    public function test_parseNumbersInStringLiterals()
    {
        $collection = Mailcode::create()->parseString(
            'Line with {setvar: $FOOBAR = "44578,45"}'
        );
        
        $this->assertTrue($collection->isValid());
    }
    
    public function test_quotesInComments()
    {
        $collection = Mailcode::create()->parseString(
            'Line with {comment: He said, "Foo me!" :D }'
        );
        
        $this->assertTrue($collection->isValid());
    }
    
    public function test_htmlEntities()
    {
        $collection = Mailcode::create()->parseString(
            '{if: 6 &gt; 2}Here{end}'
        );
        
        $message = '';
        if(!$collection->isValid())
        {
            $message = $collection->getFirstError()->getMessage();
        }
        
        $this->assertTrue($collection->isValid(), $message);
        $this->assertSame(2, $collection->countCommands());    
    }

    public function test_parseString_bracketsInStringLiterals() : void
    {
        $collection = Mailcode::create()->parseString(
            'Line with {if list-contains: $LIST.PROPERTY "[0-9]{3}" regex:}{end}.'
        );

        if(!$collection->isValid())
        {
            $this->assertTrue($collection->isValid(), $collection->getFirstError()->getMessage());
        }

        $this->assertSame(2, $collection->countCommands());
    }

    /**
     * The parser must escape regex brackets in commands, so these
     * do not break the detection of commands.
     */
    public function test_parseString_preProcess() : void
    {
        $commands = <<<'EOT'
<a href="{shownumber: $GROSS_AMOUNT "1.000,00" absolute:}">
    {if list-contains: $LIST.PROPERTY "[0-9]{3}" regex:}
        {showphone: $PHONE.NUMBER "DE" urlencode:}
        {showdate: $DATE "d.m.Y"}
    {end}
    {if contains: $VARNAME "With \"escaped\" quotes"}
        Some text here.
    {end}
</a>
EOT;
        $expected = <<<'EOT'
<a href="{shownumber: $GROSS_AMOUNT "1.000,00" absolute:}">
    {if list-contains: $LIST.PROPERTY "[0-9]%1$s3%2$s" regex:}
        {showphone: $PHONE.NUMBER "DE" urlencode:}
        {showdate: $DATE "d.m.Y"}
    {end}
    {if contains: $VARNAME "With \"escaped\" quotes"}
        Some text here.
    {end}
</a>
EOT;

        $expected = sprintf(
            $expected,
            Mailcode_Parser_StringPreProcessor::LITERAL_BRACKET_LEFT_REPLACEMENT,
            Mailcode_Parser_StringPreProcessor::LITERAL_BRACKET_RIGHT_REPLACEMENT
        );

        $processor = new Mailcode_Parser_StringPreProcessor($commands);

        $this->assertSame($expected, $processor->process());
    }

    /**
     * Using regex brackets in string literals must not
     * break the parsing of commands. All small details
     * must be taken into account:
     *
     * - A command in an HTML attribute
     * - A command with escaped quotes
     * - A command with a regular string literal
     */
    public function test_parseString_stringLiterals() : void
    {
        $commands = <<<'EOT'
<a href="{shownumber: $GROSS_AMOUNT "1.000,00" absolute:}">
    {if list-contains: $LIST.PROPERTY "[0-9]{3}" regex:}
        {showphone: $PHONE.NUMBER "DE" urlencode:}
        {showdate: $DATE "d.m.Y"}
    {end}
    {if contains: $VARNAME "With \"escaped\" quotes"}
        Some text here.
    {end}
</a>
EOT;

        $collection = Mailcode::create()->parseString($commands);

        if(!$collection->isValid())
        {
            $this->assertTrue($collection->isValid(), $collection->getFirstError()->getMessage());
        }

        $this->assertCount(7, $collection->getCommands());
    }
}
