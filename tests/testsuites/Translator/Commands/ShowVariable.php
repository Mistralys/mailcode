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
            )
        );
        
        $this->runCommands($tests);
    }
}
