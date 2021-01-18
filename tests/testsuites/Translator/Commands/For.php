<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ForTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Simple foreach',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP'),
                'expected' => '#{foreach}($LOOP in $SOURCE.list())'
            )
        );
        
        $this->runCommands($tests);
    }
}
