<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseIfBeginsWithTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'ElseIf begins with',
                'mailcode' => Mailcode_Factory::elseIf()->beginsWith('FOO.BAR', 'Search'),
                'expected' => '#elseif($StringUtils.startsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'ElseIf begins with, case insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->beginsWith('FOO.BAR', 'Search', true),
                'expected' => '#elseif($StringUtils.startsWithIgnoreCase($FOO.BAR, "Search"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
