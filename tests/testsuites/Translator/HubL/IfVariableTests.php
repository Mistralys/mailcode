<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class IfVariableTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'If var equals string',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'Value'),
                'expected' => '{% if foo.bar == "Value" %}'
            ),
            array(
                'label' => 'Case insensitive string',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'Some Text', true),
                'expected' => '{% if foo.bar|lower == "some text" %}'
            ),
            array(
                'label' => 'Boolean string value',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'true'),
                'expected' => '{% if foo.bar == "true" %}'
            ),
            array(
                'label' => 'Boolean string value, case insensitive',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'FALSE', true),
                'expected' => '{% if foo.bar|lower == "false" %}'
            ),
            array(
                'label' => 'If var equals number',
                'mailcode' => Mailcode_Factory::if()->varEquals('FOO.BAR', '42'),
                'expected' => '{% if foo.bar == 42 %}'
            ),
            array(
                'label' => 'If var equals number',
                'mailcode' => Mailcode_Factory::if()->varEqualsNumber('FOO.BAR', '42'),
                'expected' => '{% if foo.bar|float == 42 %}'
            )
        );

        $this->runCommands($tests);
    }
}
