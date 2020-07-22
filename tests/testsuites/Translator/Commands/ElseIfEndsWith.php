<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfEndsWithTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'ElseIf begins with',
                'mailcode' => Mailcode_Factory::elseIfEndsWith('FOO.BAR', 'Search'),
                'expected' => '#elseif($StringUtils.endsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'ElseIf begins with, case insensitive',
                'mailcode' => Mailcode_Factory::elseIfEndsWith('FOO.BAR', 'Search', true),
                'expected' => '#elseif($StringUtils.endsWithIgnoreCase($FOO.BAR, "Search"))'
            ),
        );
        
        $this->runCommands($tests);
    }
}
