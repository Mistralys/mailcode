<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfEmptyTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::ifEmpty('FOO.BAR'),
                'expected' => '#if($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::ifNotEmpty('FOO.BAR'),
                'expected' => '#if(!$StringUtils.isEmpty($FOO.BAR))'
            )
        );
        
        $this->runCommands($tests);
    }
}
