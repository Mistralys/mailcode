<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfVarTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'ElseIf var equals string',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsString('FOO.BAR', 'Value'),
                'expected' => '#elseif($FOO.BAR == "Value")'
            )
        );
        
        $this->runCommands($tests);
    }
}
