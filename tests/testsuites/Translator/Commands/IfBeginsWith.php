<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfBeginsWithTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If begins with',
                'mailcode' => Mailcode_Factory::ifBeginsWith('FOO.BAR', 'Search'),
                'expected' => '#if($StringUtils.startsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'If begins with, case insensitive',
                'mailcode' => Mailcode_Factory::ifBeginsWith('FOO.BAR', 'Search', true),
                'expected' => '#if($StringUtils.startsWithIgnoreCase($FOO.BAR, "Search"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
