<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfNotContainsTests extends VelocityTestCase
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
                'label' => 'ElseIf not contains',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('FOO.BAR', array('Value')),
                'expected' => '#elseif(!$FOO.BAR.matches("(?s).*Value.*"))'
            ),
            array(
                'label' => 'ElseIf not contains with slash',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('FOO.BAR', array('Va\lue')),
                'expected' => sprintf(
                    '#elseif(!$FOO.BAR.matches("(?s).*%s.*"))',
                    'Va[DBLSLASH]lue'
                )
            ),
            array(
                'label' => 'ElseIf not contains with special characters',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('FOO.BAR', array('6 + 4 * 3')),
                'expected' => sprintf(
                    '#elseif(!$FOO.BAR.matches("(?s).*%s.*"))',
                    '6 [SLASH]+ 4 [SLASH]* 3'
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#elseif(!$FOO.BAR.matches("(?s).*Foo.*") && !$FOO.BAR.matches("(?s).*Bar.*"))'
            ),
            array(
                'label' => 'With quotes in search term',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('FOO.BAR', array('Value, "weird" huh?')),
                'expected' => sprintf(
                    '#elseif(!$FOO.BAR.matches("(?s).*%s.*"))',
                    'Value, [SLASH]"weird[SLASH]" huh[SLASH]?'
                )
            )
        );
        
        $this->runCommands($tests);
    }
}
