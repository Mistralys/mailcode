<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection_Error_Command;
use Mailcode\Mailcode_Collection_Error_Message;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Parser;

final class Parser_ParserTests extends MailcodeTestCase
{
    public function test_createParser()
    {
        $parser = Mailcode::create()->getParser();
        
        $this->assertInstanceOf(Mailcode_Parser::class, $parser);
    }
    
    public function test_parseString_withoutCommands()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString("Some text without commands");
        
        $this->assertSame(0, $collection->countCommands());
        $this->assertFalse($collection->hasCommands());
    }

    public function test_parseString_withSingleCommand()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            "Line with {showvar: \$CUSTOMER.NAME}"
        );
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
    }
    
    public function test_parseString_caseInsensitive()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            "Line with {ShowVar: \$CUSTOMER.NAME}"
        );
        
        $this->assertSame(1, $collection->countCommands());
        $this->assertTrue($collection->hasCommands());
    }
    
    public function test_parseString_freeSpacing()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
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

    public function test_parseString_withSeveralCommands()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            "Line with {showvar: \$CUSTOMER.NAME}
            and another one further {showvar: \$ADDRESS.LINE1} down."
        );
        
        $this->assertSame(2, $collection->countCommands());
    }

    public function test_parseString_withDuplicateCommands()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
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
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
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
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
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
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            "Line with {showvar: \$FOO.8AR}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT, $errors[0]->getCode());
    }

    public function test_parseString_invalidVariablePath()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            "Line with {showvar: \$3OO.BAR}"
        );
        
        $this->assertFalse($collection->isValid());
        
        $errors = $collection->getErrors();
        
        $this->assertInstanceof(Mailcode_Collection_Error_Command::class, $errors[0]);
        $this->assertSame(Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT, $errors[0]->getCode());
    }
    
    public function test_parseString_noVariable()
    {
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
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
        $parser = Mailcode::create()->getParser();
        
        $collection = $parser->parseString(
            'Line with {setvar: $FOOBAR = "44578,45"}'
        );
        
        $this->assertTrue($collection->isValid());
    }
}
