<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;
use VelocityTestCase;

final class SetVariableTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'String value, path only variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO', 'Value'),
                'expected' => '{% set foo = "Value" %}'
            ),
            array(
                'label' => 'String value, dot variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', 'Value'),
                'expected' => '{% set foo.bar = "Value" %}'
            ),
            array(
                'label' => 'Count variable',
                'mailcode' => Mailcode_Factory::set()->varCount('FOO.BAR', '$FOO.COUNT'),
                'expected' => '{% set foo.bar = foo.count|length %}'
            ),
            array(
                'label' => 'Count variable with different path',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', '$BER.COUNT', true, true),
                'expected' => '{% set foo.bar = ber.count|length %}'
            ),
            array(
                'label' => 'Count variable with path only',
                'mailcode' => Mailcode_Factory::set()->var('FOO', '$BAR.COUNT', true, true),
                'expected' => '{% set foo = bar.count|length %}'
            ),
            array(
                'label' => 'Count and source variables with path only',
                'mailcode' => Mailcode_Factory::set()->var('FOO', '$BAR', true, true),
                'expected' => '{% set foo = bar|length %}'
            )
        );

        $this->runCommands($tests);
    }
}
