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
            )
        );
        
        $this->runCommands($tests);
    }
}
