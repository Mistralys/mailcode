<?php

declare(strict_types=1);

namespace MailcodeTests\Commands;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection_NestingValidator;
use MailcodeTestCase;

final class NestingTests extends MailcodeTestCase
{
    public function test_missingOpening(): void
    {
        $string =
            '{elseif variable: $FOO.BAR==""}';

        $collection = Mailcode::create()->parseString($string);

        $this->assertFalse($collection->isValid());
        $this->assertTrue($collection->hasErrorCode(Mailcode_Collection_NestingValidator::VALIDATION_SIBLING_WITHOUT_PARENT));
    }

    public function test_missingClosing(): void
    {
        $string =
            '{if variable: $FOO.BAR==""}';

        $collection = Mailcode::create()->parseString($string);

        $this->assertFalse($collection->isValid());
        $this->assertTrue($collection->hasErrorCode(Mailcode_Collection_NestingValidator::VALIDATION_UNCLOSED_COMMAND));
    }

    public function test_neverOpened(): void
    {
        $string =
            '{end}';

        $collection = Mailcode::create()->parseString($string);

        $this->assertFalse($collection->isValid());
        $this->assertTrue($collection->hasErrorCode(Mailcode_Collection_NestingValidator::VALIDATION_COMMANDS_ALREADY_CLOSED));
    }

    public function test_multilevelNesting(): void
    {
        $string =
            '{if variable: $FOO.BAR == "Yes"}
            {comment: Determine barfoo value}
            {if variable: $BAR.FOO == "One"}
                One
            {elseif variable: $BAR.FOO == "Two"}
                Two
            {else}
                {if variable: $LOPOS.NAME == "Name"}
                    Catch all
                {end}
            {end}
        {end}';

        $collection = Mailcode::create()->parseString($string);

        $errors = $collection->getErrors();

        foreach ($errors as $error) {
            echo $error->getMessage();
        }

        $this->assertTrue($collection->isValid());
    }

    public function test_wrongParent(): void
    {
        $string =
            '{for: $NAME in: $FOO}
            {else}
        {end}';

        $collection = Mailcode::create()->parseString($string);
        $errors = $collection->getErrors();

        $this->assertCount(1, $errors);

        $error = $errors[0];

        $this->assertFalse($collection->isValid());
        $this->assertSame($error->getCode(), Mailcode_Collection_NestingValidator::VALIDATION_SIBLING_WRONG_PARENT);
    }
}
