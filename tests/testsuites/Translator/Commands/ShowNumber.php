<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowNumberTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Show number, default format',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR'),
                'expected' => '${number.format(\'###,###.##\', $number.toNumber(\'##.##\', $FOO.BAR))}'
            ),
        );
        
        $this->runCommands($tests);
    }
}
