<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\Commands;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class SetVariableTests extends VelocityTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'String value, path only variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO', 'Value'),
                'expected' => '#set($FOO = "Value")'
            ),
            array(
                'label' => 'String value, dot variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', 'Value'),
                'expected' => '#set($FOO.BAR = "Value")'
            ),
            array(
                'label' => 'Count variable',
                'mailcode' => Mailcode_Factory::set()->varCount('FOO.BAR', '$FOO.COUNT'),
                'expected' => '#set($FOO.BAR = $map.of($FOO).keys("COUNT").count())'
            ),
            array(
                'label' => 'Count variable with different path',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', '$BER.COUNT', true, true),
                'expected' => '#set($FOO.BAR = $map.of($BER).keys("COUNT").count())'
            ),
            array(
                'label' => 'Count variable with path only',
                'mailcode' => Mailcode_Factory::set()->var('FOO', '$BAR.COUNT', true, true),
                'expected' => '#set($FOO = $map.of($BAR).keys("COUNT").count())'
            ),
            array(
                'label' => 'Count and source variables with path only',
                'mailcode' => Mailcode_Factory::set()->var('FOO', '$BAR', true, true),
                'expected' => '#set($FOO = $map.of($BAR).count())'
            )
        );

        $this->runCommands($tests);
    }

    /**
     * Check the case where two same name variables are not
     * tokenized correctly, causing an "Unquoted string literal
     * '.COUNT'" error.
     */
    public function test_weirdParseBehavior(): void
    {
        $collection = Mailcode::create()->parseString('{setvar: $FOO count=$FOO.COUNT}');

        if (!$collection->isValid()) {
            $this->fail($collection->getFirstError()->getMessage());
        }

        $command = $collection->getFirstCommand();
        $this->assertInstanceOf(Mailcode_Commands_Command_SetVariable::class, $command);
    }
}
