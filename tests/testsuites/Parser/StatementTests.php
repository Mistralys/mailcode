<?php

declare(strict_types=1);

namespace testsuites\Parser;

use Mailcode\Mailcode_Parser_Statement;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_ParamName;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use MailcodeTestCase;

class StatementTests extends MailcodeTestCase
{
    public function test_copy() : void
    {
        $subject = '$FOO urlencode: "String1" 42 "String2"';
        $statement = new Mailcode_Parser_Statement($subject);

        $this->assertCount(5, $statement->getInfo()->getTokens());

        $copy = $statement->copy();

        $this->assertCount(5, $copy->getInfo()->getTokens());
        $this->assertSame($statement->getNormalized(), $copy->getNormalized());
    }

    /**
     * Test case for a bug, where copying a statement
     * that was modified programmatically would not
     * include the modified tokens.
     */
    public function test_addProgrammatically() : void
    {
        $statement = new Mailcode_Parser_Statement('');
        $info = $statement->getInfo();

        $info->addStringLiteral('String1');

        $copy = $statement->copy();

        $this->assertSame('"String1"', $copy->getNormalized());
    }

    public function test_namedParameters() : void
    {
        $tests = array(
            array(
                'label' => 'With string literal',
                'string' => 'name="param value"',
                'name' => 'name'
            ),
            array(
                'label' => 'Name with dashes',
                'string' => 'break-at="param value"',
                'name' => 'break-at'
            ),
            array(
                'label' => 'Name with numbers',
                'string' => 'foo42="param value"',
                'name' => 'foo42'
            )
        );

        foreach($tests as $test)
        {
            $statement = new Mailcode_Parser_Statement($test['string']);
            $info = $statement->getInfo();

            $this->assertCount(2, $info->getTokens());

            $tokens = $info->getTokens();
            $this->assertArrayHasKey(1, $tokens);

            $this->assertSame($test['name'], $tokens[1]->getName());
            $this->assertStringContainsString($test['name'].'=', $statement->getNormalized());
        }
    }

    public function test_namedParameterGetParamName() : void
    {
        $statement = new Mailcode_Parser_Statement('name="param value"');

        $info = $statement->getInfo();

        $name = $info->getTokenByIndex(0);

        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_ParamName::class, $name);
        $this->assertSame('name', $name->getParamName());
    }

    public function test_namedParameterIsSpacingAgnostic() : void
    {
        $statement = new Mailcode_Parser_Statement('$FOO = 42 "value" name   =    
        "param value"');

        $info = $statement->getInfo();

        $this->assertCount(6, $info->getTokens());

        $literals = $info->getStringLiterals();
        $this->assertArrayHasKey(1, $literals);

        $this->assertSame('name', $literals[1]->getName());
        $this->assertStringContainsString('name="param value"', $statement->getNormalized());
    }

    public function test_setNameAddsNameToken() : void
    {
        $statement = new Mailcode_Parser_Statement('name="param value"');

        $info = $statement->getInfo();

        $stringLiteral = $info->getTokenByIndex(1);

        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $stringLiteral);

        $info->setParamName($stringLiteral, 'foo');

        $this->assertSame('foo', $stringLiteral->getName());
        $this->assertSame('foo="param value"', $statement->getNormalized());
    }

    /**
     * When removing a named token, the name must be removed as well.
     */
    public function test_removeNamedToken() : void
    {
        $statement = new Mailcode_Parser_Statement('name="param value"');

        $info = $statement->getInfo();

        $stringLiteral = $info->getTokenByIndex(1);

        $this->assertNotNull($stringLiteral);
        $info->removeToken($stringLiteral);

        $this->assertSame('', $statement->getNormalized());
    }
}
