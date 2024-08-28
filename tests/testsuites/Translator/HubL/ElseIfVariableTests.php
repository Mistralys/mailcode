<?php

declare(strict_types=1);

namespace testsuites\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ElseIfVariableTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'If var equals string',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'Value'),
                'expected' => '{% elif foo.bar == "Value" %}'
            ),
            array(
                'label' => 'Case insensitive string',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'Some Text', true),
                'expected' => '{% elif foo.bar|lower == "some text" %}'
            ),
            array(
                'label' => 'Boolean string value',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'true'),
                'expected' => '{% elif foo.bar == "true" %}'
            ),
            array(
                'label' => 'Boolean string value, case insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'FALSE', true),
                'expected' => '{% elif foo.bar|lower == "false" %}'
            ),
            array(
                'label' => 'If var equals number',
                'mailcode' => Mailcode_Factory::elseIf()->varEquals('FOO.BAR', '42'),
                'expected' => '{% elif foo.bar == 42 %}'
            ),
            array(
                'label' => 'If var equals number',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsNumber('FOO.BAR', '42'),
                'expected' => '{% elif foo.bar|float == 42 %}'
            )
        );

        $this->runCommands($tests);
    }
}
