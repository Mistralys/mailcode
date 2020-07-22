<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowDateTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Show date, default format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR'),
                'expected' => '${date.format("yyyy/M/d", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, german format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.Y H:i:s'),
                'expected' => '${date.format("d.M.yyyy H:m:s", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, short year format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.y'),
                'expected' => '${date.format("d.M.yy", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            )
        );
        
        $this->runCommands($tests);
    }
}
