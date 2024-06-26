<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseIfEndsWithTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'ElseIf begins with',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('FOO.BAR', 'Search'),
                'expected' => '#elseif($StringUtils.endsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'ElseIf begins with, case insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('FOO.BAR', 'Search', true),
                'expected' => '#elseif($StringUtils.endsWithIgnoreCase($FOO.BAR, "Search"))'
            ),
        );
        
        $this->runCommands($tests);
    }
}
