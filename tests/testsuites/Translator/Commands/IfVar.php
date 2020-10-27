<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfVarTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If var equals string',
                'mailcode' => Mailcode_Factory::ifVarEqualsString('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR == "Value")'
            ),
            array(
                'label' => 'Boolean value',
                'mailcode' => Mailcode_Factory::ifVarEqualsString('FOO.BAR', 'true'),
                'expected' => '#if($FOO.BAR.toLowerCase() == "true")'
            ),
            array(
                'label' => 'Boolean value, case insensitive',
                'mailcode' => Mailcode_Factory::ifVarEqualsString('FOO.BAR', 'FALSE'),
                'expected' => '#if($FOO.BAR.toLowerCase() == "false")'
            )
        );
        
        $this->runCommands($tests);
    }
}
