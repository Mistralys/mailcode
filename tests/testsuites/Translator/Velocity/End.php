<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_EndTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'End',
                'mailcode' => Mailcode_Factory::misc()->end(),
                'expected' => '#{end}'
            )
        );
        
        $this->runCommands($tests);
    }
}
