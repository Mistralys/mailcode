<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_IfEmptyTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::if()->empty('FOO.BAR'),
                'expected' => '#if($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::if()->notEmpty('FOO.BAR'),
                'expected' => '#if(!$StringUtils.isEmpty($FOO.BAR))'
            )
        );
        
        $this->runCommands($tests);
    }
}
