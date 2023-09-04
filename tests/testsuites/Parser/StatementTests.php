<?php

declare(strict_types=1);

namespace testsuites\Parser;

use Mailcode\Mailcode_Parser_Statement;
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

    public function test_namedParameter() : void
    {
        $statement = new Mailcode_Parser_Statement('"value" name="param value"');
        $info = $statement->getInfo();

        $this->assertCount(3, $info->getTokens());

        $literals = $info->getStringLiterals();
        $this->assertArrayHasKey(1, $literals);

        $this->assertSame('name', $literals[1]->getName());
    }
}
