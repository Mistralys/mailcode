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
}
