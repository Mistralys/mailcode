<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ForTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Simple foreach',
                'mailcode' => Mailcode_Factory::for('SOURCE', 'LOOP'),
                'expected' => '#{foreach}($LOOP in $SOURCE)'
            )
        );
        
        $this->runCommands($tests);
    }
}
