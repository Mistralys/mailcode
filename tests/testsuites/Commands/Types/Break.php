<?php

declare(strict_types=1);

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection;
use Mailcode\Mailcode_Commands_Command_Break;
use Mailcode\Mailcode_Factory;

final class Mailcode_BreakTests extends MailcodeTestCase
{
    public function test_validation_standalone() : void
    {
        $tests = array(
            array(
                'label' => 'Without surrounding for',
                'string' => '{break}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Break::VALIDATION_NO_PARENT_FOR
            ),
            array(
                'label' => 'Valid with surrounding for',
                'string' => '{for: $RECORD in: $SOURCE}{break}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid with multiple nesting levels',
                'string' =>
                    '{for: $RECORD in: $SOURCE}'.
                        '{if contains: $RECORD.TEXT "Search"}'.
                            '{break}'.
                        '{end}'.
                    '{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }

    public function test_validation_safeguardValid() : void
    {
        $code = <<<'EOT'
{for: $RECORD in: $SOURCE}
    {break}
{end}
EOT;

        $safeguard = Mailcode::create()->createSafeguard($code);
        $safe = $safeguard->makeSafe();

        $this->assertTrue($safeguard->isValid());
    }

    public function test_validation_safeguardInvalid() : void
    {
        $code = <<<'EOT'
{if contains: $RECORD "Search"}
    {break}
{end}
EOT;

        $safeguard = Mailcode::create()->createSafeguard($code);

        $this->expectException(\Mailcode\Mailcode_Exception::class);

        $safeguard->makeSafe();
    }

    public function test_highlight() : void
    {
        $end = Mailcode_Factory::break();

        $expected = '<span class="mailcode-bracket">{</span><span class="mailcode-command-name">break</span><span class="mailcode-bracket">}</span>';
        
        $this->assertSame($expected, $end->getHighlighted());
    }

    public function test_manualNesting() : void
    {
        $for = Mailcode_Factory::for('SOURCE', 'RECORD');
        $end = Mailcode_Factory::end();
        $break = Mailcode_Factory::break();

        $break->setParent($for);

        $collection = new Mailcode_Collection();
        $collection->addCommand($for);
        $collection->addCommand($end);
        $collection->addCommand($break);

        $collection->finalize();

        $this->assertTrue($collection->isValid());
    }

    public function test_manualNesting_error() : void
    {
        $for = Mailcode_Factory::for('SOURCE', 'RECORD');
        $end = Mailcode_Factory::end();
        $break = Mailcode_Factory::break();

        // Not setting the break command's parent

        $collection = new Mailcode_Collection();
        $collection->addCommand($for);
        $collection->addCommand($end);
        $collection->addCommand($break);

        $collection->finalize();

        $this->assertFalse($collection->isValid());
        $this->assertEquals(
            Mailcode_Commands_Command_Break::VALIDATION_NO_PARENT_FOR,
            $collection->getFirstError()->getCode()
        );
    }
}
