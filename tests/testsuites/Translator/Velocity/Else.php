<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseTests extends VelocityTestCase
{
    public function test_translateCommand() : void
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
