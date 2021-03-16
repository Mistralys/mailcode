<?php

declare(strict_types=1);

use Mailcode\Mailcode;

final class PreProcessor_ParseTests extends MailcodeTestCase
{
    public function test_preProcess() : void
    {
        $subject = "Some {mono}monospace text{end}.";
        $expected = "Some <code>monospace text</code>.";

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_preProcess_nestedCommands() : void
    {
        $subject = 'Some {mono}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <code>monospace {showvar: $FOOBAR} text</code>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_multiline() : void
    {
        $subject = 'Some {mono: multiline:}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <pre>monospace {showvar: $FOOBAR} text</pre>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_class() : void
    {
        $subject = 'Some {mono: multiline: "className"}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <pre class="className">monospace {showvar: $FOOBAR} text</pre>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_classMulti() : void
    {
        $subject = 'Some {mono: multiline: "className" "otherClass"}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <pre class="className otherClass">monospace {showvar: $FOOBAR} text</pre>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_classIgnoreDuplicates() : void
    {
        $subject = 'Some {mono: multiline: "className" "className"}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <pre class="className">monospace {showvar: $FOOBAR} text</pre>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_classSameLiteral() : void
    {
        $subject = 'Some {mono: multiline: "className other className"}monospace {showvar: $FOOBAR} text{end}.';
        $expected = 'Some <pre class="className other">monospace {showvar: $FOOBAR} text</pre>.';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals($expected, $result);
    }

    public function test_mono_classInvalid() : void
    {
        $subject = 'Some {mono: multiline: "[class]"}monospace {showvar: $FOOBAR} text{end}.';
        $expected = $subject;

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertFalse($processor->isValid());
        $this->assertEquals($expected, $result);
    }
}
