<?php

declare(strict_types=1);

namespace testsuites\Parser;

use Mailcode\Parser\Statement\Tokenizer\SpecialChars;
use MailcodeTestCase;

class TokenizerSpecialCharsTests extends MailcodeTestCase
{
    private string $encodedString =
        SpecialChars::PLACEHOLDER_QUOTE.
        SpecialChars::PLACEHOLDER_BRACKET_OPEN.
        SpecialChars::PLACEHOLDER_BRACKET_CLOSE;

    private string $escapedString = '\"\{\}';

    private string $rawString = '"{}';

    /**
     * Encoding a string must work on all possible
     * variations of a string, including already
     * encoded or escaped strings.
     */
    public function test_encodeAll() : void
    {
        $this->assertSame(
            $this->encodedString,
            SpecialChars::encodeAll($this->rawString)
        );

        $this->assertSame(
            $this->encodedString,
            SpecialChars::encodeAll($this->escapedString)
        );

        $this->assertSame(
            $this->encodedString,
            SpecialChars::encodeAll($this->encodedString)
        );
    }

    public function test_encodeEscaped() : void
    {
        $this->assertSame(
            $this->encodedString,
            SpecialChars::encodeEscaped($this->escapedString)
        );

        $this->assertSame(
            $this->encodedString,
            SpecialChars::encodeEscaped($this->encodedString)
        );

        $this->assertSame(
            $this->rawString,
            SpecialChars::encodeEscaped($this->rawString)
        );
    }

    public function test_decode() : void
    {
        $this->assertSame(
            $this->rawString,
            SpecialChars::decode($this->encodedString)
        );

        $this->assertSame(
            $this->rawString,
            SpecialChars::decode($this->escapedString)
        );

        $this->assertSame(
            $this->rawString,
            SpecialChars::decode($this->rawString)
        );
    }

    public function test_escape() : void
    {
        $this->assertSame(
            $this->escapedString,
            SpecialChars::escape($this->encodedString)
        );

        $this->assertSame(
            $this->escapedString,
            SpecialChars::escape($this->escapedString)
        );

        $this->assertSame(
            $this->escapedString,
            SpecialChars::escape($this->rawString)
        );
    }
}
