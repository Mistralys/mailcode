<?php

use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;

final class Translator_Velocity_SetVariableTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        try {

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
                    'mailcode' => Mailcode_Factory::set()->var('FOO', '$FOO.COUNT', true, true),
                    'expected' => '#set($FOO = $map.of($BAR).keys("COUNT").count())'
                )
            );
        } catch (Mailcode_Exception $e) {
            $this->fail('Exception triggered: ' . $e->getMessage() . ' | ' . $e->getDetails());
        }

        $this->runCommands($tests);
    }
}
