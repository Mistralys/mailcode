<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_BreeakTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Break',
                'mailcode' => Mailcode_Factory::misc()->break(),
                'expected' => '#{break}'
            )
        );
        
        $this->runCommands($tests);
    }
}
