<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseIfVarTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'ElseIf var equals string',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'Value'),
                'expected' => '#elseif($FOO.BAR == "Value")'
            ),
            array(
                'label' => 'Case insensitive string',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'Some Text', true),
                'expected' => '#elseif($FOO.BAR.toLowerCase() == "some text")'
            ),
            array(
                'label' => 'Boolean value',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'true'),
                'expected' => '#elseif($FOO.BAR.toLowerCase() == "true")'
            ),
            array(
                'label' => 'Boolean value, case insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->varEqualsString('FOO.BAR', 'FALSE'),
                'expected' => '#elseif($FOO.BAR.toLowerCase() == "false")'
            )
        );
        
        $this->runCommands($tests);
    }
}
