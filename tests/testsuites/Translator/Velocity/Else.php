<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Else',
                'mailcode' => Mailcode_Factory::elseIf()->else(),
                'expected' => '#{else}'
            )
        );
        
        $this->runCommands($tests);
    }
}
