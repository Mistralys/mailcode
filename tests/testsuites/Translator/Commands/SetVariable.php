<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_SetVariableTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::set()->setVar('FOO.BAR', 'Value', true),
                'expected' => '#set($FOO.BAR = "Value")'
            )
        );
        
        $this->runCommands($tests);
    }
}
