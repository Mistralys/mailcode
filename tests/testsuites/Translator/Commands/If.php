<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If',
                'mailcode' => Mailcode_Factory::if()->if('$FOO.BAR + 2 == 45'),
                'expected' => '#if($FOO.BAR + 2 == 45)'
            ),
            array(
                'label' => 'If',
                'mailcode' => Mailcode_Factory::if()->if('$FOO.BAR != "TRUE"'),
                'expected' => '#if($FOO.BAR != "TRUE")'
            ),
            array(
                'label' => 'If with Velocity syntax',
                'mailcode' => Mailcode_Factory::if()->if('$FOO.BAR.urldecode().match(".*[?].*")'),
                'expected' => '#if($FOO.BAR.urldecode().match(".*[?].*"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
