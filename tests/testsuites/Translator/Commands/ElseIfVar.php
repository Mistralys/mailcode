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
            ),
            array(
                'label' => 'Boolean value',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsString('FOO.BAR', 'true'),
                'expected' => '#elseif($FOO.BAR.toLowerCase() == "true")'
            ),
            array(
                'label' => 'Boolean value, case insensitive',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsString('FOO.BAR', 'FALSE'),
                'expected' => '#elseif($FOO.BAR.toLowerCase() == "false")'
            )
        );
        
        $this->runCommands($tests);
    }
}
