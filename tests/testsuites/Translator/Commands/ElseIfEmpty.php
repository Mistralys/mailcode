<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfEmptyTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'ElseIf empty',
                'mailcode' => Mailcode_Factory::elseIfEmpty('FOO.BAR'),
                'expected' => '#elseif($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'ElseIf not empty',
                'mailcode' => Mailcode_Factory::elseIfNotEmpty('FOO.BAR'),
                'expected' => '#elseif(!$StringUtils.isEmpty($FOO.BAR))'
            )
        );
        
        $this->runCommands($tests);
    }
}
