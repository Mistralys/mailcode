<?php

declare(strict_types=1);

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_SetVariable;
use Mailcode\Mailcode_Factory;

final class Translator_Velocity_SetVariableTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO', 'Value'),
                'expected' => '#set($FOO = "Value")'
            ),
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', 'Value'),
                'expected' => '#set($FOO.BAR = "Value")'
            ),
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', '$FOO.COUNT', true, true),
                'expected' => '#set($FOO.BAR = $map.of($FOO).keys("COUNT").count())'
            ),
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO.BAR', '$BER.COUNT', true, true),
                'expected' => '#set($FOO.BAR = $map.of($BER).keys("COUNT").count())'
            ),
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->var('FOO', '$BAR.COUNT', true, true),
                'expected' => '#set($FOO = $map.of($BAR).keys("COUNT").count())'
            )
        );

        $this->runCommands($tests);
    }

    /**
     * Check the case where two same name variables are not
     * tokenized correctly, causing a "Unquoted string literal
     * '.COUNT'" error.
     */
    public function test_weirdParseBehavior() : void
    {
        $collection = Mailcode::create()->parseString('{setvar: $FOO count: $FOO.COUNT}');

        if(!$collection->isValid()) {
            $this->fail($collection->getFirstError()->getMessage());
        }

        $command = $collection->getFirstCommand();
        $this->assertInstanceOf(Mailcode_Commands_Command_SetVariable::class, $command);
    }
}
