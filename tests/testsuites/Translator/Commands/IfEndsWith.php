<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfEndsWithTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If ends with',
                'mailcode' => Mailcode_Factory::ifEndsWith('FOO.BAR', 'Search'),
                'expected' => '#if($StringUtils.endsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'If ends with, case insensitive',
                'mailcode' => Mailcode_Factory::ifEndsWith('FOO.BAR', 'Search', true),
                'expected' => '#if($StringUtils.endsWithIgnoreCase($FOO.BAR, "Search"))'
            ),
        );
        
        $this->runCommands($tests);
    }
}
