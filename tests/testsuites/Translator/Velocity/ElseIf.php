<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseIfTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Else if',
                'mailcode' => Mailcode_Factory::elseIf()->elseIf('$FOO.BAR + 2 == 45'),
                'expected' => '#elseif($FOO.BAR + 2 == 45)'
            ),
            array(
                'label' => 'If with Velocity syntax',
                'mailcode' => Mailcode_Factory::elseIf()->elseIf('$FOO.BAR.urldecode().match(".*[?].*")'),
                'expected' => '#elseif($FOO.BAR.urldecode().match(".*[?].*"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
