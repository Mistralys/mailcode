<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowVariableTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Show variable',
                'mailcode' => Mailcode_Factory::showVar('FOO.BAR'),
                'expected' => '${FOO.BAR}'
            ),
            array(
                'label' => 'Show variable, URL encoded',
                'mailcode' => Mailcode_Factory::showVar('FOO.BAR')->setURLEncoding(true),
                'expected' => '${esc.url($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, URL decoded',
                'mailcode' => Mailcode_Factory::showVar('FOO.BAR'),
                'expected' => '${esc.url($FOO.BAR)}'
            )
        );
        
        $this->runCommands($tests);
    }
}
