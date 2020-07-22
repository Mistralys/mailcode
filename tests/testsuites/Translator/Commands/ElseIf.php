<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Else if',
                'mailcode' => Mailcode_Factory::elseIf('$FOO.BAR + 2 == 45'),
                'expected' => '#elseif($FOO.BAR + 2 == 45)'
            )
        );
        
        $this->runCommands($tests);
    }
}
