<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfNotContainsTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        // NOTE: for readability purposes here in the tests,
        // the expected amount of slashes are replaced by
        // strings ([SLASH], [DBLSLASH]...).
        //
        // Otherwise, since backslashes have to be escaped
        // in PHP in some cases, the examples here would be
        // pretty cryptic.
        
        $tests = array(
            array(
                'label' => 'If not contains',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('Value')),
                'expected' => '#if(!$FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'If not contains with slash',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('Va\lue')),
                'expected' => sprintf(
                    '#if(!$FOO.BAR.matches("(?s)%s"))',
                    'Va[DBLSLASH]lue'
                )
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('6 + 4 * 3')),
                'expected' => sprintf(
                    '#if(!$FOO.BAR.matches("(?s)%s"))',
                    addslashes('6 [SLASH]+ 4 [SLASH]* 3')
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#if(!$FOO.BAR.matches("(?s)Foo") && !$FOO.BAR.matches("(?s)Bar"))'
            ),
            array(
                'label' => 'If not contains with slash',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('Value, "quoted" yeah?')),
                'expected' => sprintf(
                    '#if(!$FOO.BAR.matches("(?s)%s"))',
                    'Value, [SLASH]"quoted[SLASH]" yeah[SLASH]?'
                )
            ),
            array(
                'label' => 'If not contains with slash',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO.BAR', array('(Value)')),
                'expected' => sprintf(
                    '#if(!$FOO.BAR.matches("(?s)%s"))',
                    '[SLASH](Value[SLASH])'
                )
            )
        );
        
        $this->runCommands($tests);
    }
}
