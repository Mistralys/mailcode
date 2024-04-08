<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_IfVarTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'If var equals string',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR == "Value")'
            ),
            array(
                'label' => 'Case insensitive string',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'Some Text', true),
                'expected' => '#if($FOO.BAR.toLowerCase() == "some text")'
            ),
            array(
                'label' => 'Boolean value',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'true'),
                'expected' => '#if($FOO.BAR.toLowerCase() == "true")'
            ),
            array(
                'label' => 'Boolean value, case insensitive',
                'mailcode' => Mailcode_Factory::if()->varEqualsString('FOO.BAR', 'FALSE'),
                'expected' => '#if($FOO.BAR.toLowerCase() == "false")'
            )
        );
        
        $this->runCommands($tests);
    }
}
