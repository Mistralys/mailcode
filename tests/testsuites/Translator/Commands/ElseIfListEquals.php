<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ElseIfListEqualsTests extends VelocityTestCase
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
                'label' => 'ElseIf list contains',
                'mailcode' => Mailcode_Factory::elseIf()->listEquals('FOO.BAR', array('Value')),
                'expected' => '#elseif($map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]AValue[DBLSLASH]Z"))'
            ),
            array(
                'label' => 'ElseIf contains with slash',
                'mailcode' => Mailcode_Factory::elseIf()->listEquals('FOO.BAR', array('Va\lue')),
                'expected' => sprintf(
                    '#elseif($map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]A%s[DBLSLASH]Z"))',
                    'Va[DBLSLASH]lue'
                )
            ),
            array(
                'label' => 'ElseIf contains with special characters',
                'mailcode' => Mailcode_Factory::elseIf()->listEquals('FOO.BAR', array('6 + 4 * 3')),
                'expected' => sprintf(
                    '#elseif($map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]A%s[DBLSLASH]Z"))',
                    '6 [SLASH]+ 4 [SLASH]* 3'
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::elseIf()->listEquals('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#elseif($map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]AFoo[DBLSLASH]Z") || $map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]ABar[DBLSLASH]Z"))'
            ),
            array(
                'label' => 'With quotes in search term',
                'mailcode' => Mailcode_Factory::elseIf()->listEquals('FOO.BAR', array('Value, "weird" huh?')),
                'expected' => sprintf(
                    '#elseif($map.hasElement($FOO.list(), "BAR", "(?s)[DBLSLASH]A%s[DBLSLASH]Z"))',
                    'Value, [SLASH]"weird[SLASH]" huh[SLASH]?'
                )
            )
        );
        
        $this->runCommands($tests);
    }
}