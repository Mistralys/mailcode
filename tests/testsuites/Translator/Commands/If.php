<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If',
                'mailcode' => Mailcode_Factory::if('$FOO.BAR + 2 == 45'),
                'expected' => '#if($FOO.BAR + 2 == 45)'
            )
        );
        
        $this->runCommands($tests);
    }
}
