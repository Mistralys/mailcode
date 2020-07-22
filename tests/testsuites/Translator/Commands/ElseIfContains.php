<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfContainsTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'ElseIf contains',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', 'Value'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'ElseIf contains with slash',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', 'Va\lue'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)Va\\\\lue"))'
            ),
            array(
                'label' => 'ElseIf contains with special characters',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)6 \+ 4 \* 3"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
