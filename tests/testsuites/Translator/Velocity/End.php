<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_EndTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'End',
                'mailcode' => Mailcode_Factory::misc()->end(),
                'expected' => '#{end}'
            )
        );
        
        $this->runCommands($tests);
    }
}
