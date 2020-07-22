<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfContainsTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If contains',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Va\lue'),
                'expected' => '#if($FOO.BAR.matches("(?s)Va\\\\lue"))'
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => '#if($FOO.BAR.matches("(?s)6 \+ 4 \* 3"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
