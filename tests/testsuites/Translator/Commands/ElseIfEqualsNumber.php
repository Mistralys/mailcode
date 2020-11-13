<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfEqualsNumberTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Integer value',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsNumber('FOO.BAR', '100'),
                'expected' => "#elseif(\$FOO.BAR.replace(',', '.') == 100)"
            ),
            array(
                'label' => 'Value with comma',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsNumber('FOO.BAR', '45,12'),
                'expected' => "#elseif(\$FOO.BAR.replace(',', '.') == 45.12)"
            ),
            array(
                'label' => 'Value with dot',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsNumber('FOO.BAR', '45.12'),
                'expected' => "#elseif(\$FOO.BAR.replace(',', '.') == 45.12)"
            )
        );
        
        $this->runCommands($tests);
    }
}
