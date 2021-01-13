<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_BreeakTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Break',
                'mailcode' => Mailcode_Factory::break(),
                'expected' => '#{break}'
            )
        );
        
        $this->runCommands($tests);
    }
}
