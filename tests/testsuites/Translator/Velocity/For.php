<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ForTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Simple foreach',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP'),
                'expected' => '#{foreach}($LOOP in $SOURCE.list())'
            ),
            array(
                'label' => 'Foreach with break criteria',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP', '13'),
                'expected' => '#{foreach}($LOOP in $SOURCE.list())#if($foreach.count > 13)#{break}#{end}'
            ),
            array(
                'label' => 'Foreach with break criteria',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP', '$FOO.BAR'),
                'expected' => '#{foreach}($LOOP in $SOURCE.list())#if($foreach.count > $FOO.BAR)#{break}#{end}'
            )
        );

        $this->runCommands($tests);
    }
}
