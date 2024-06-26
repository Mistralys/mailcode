<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_IfListEqualsTests extends VelocityTestCase
{
    public function test_translateCommand() : void
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
                'label' => 'If list contains',
                'mailcode' => Mailcode_Factory::if()->listEquals('FOO.BAR', array('Value')),
                'expected' => '#if($map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]AValue[SLASH]Z"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::if()->listEquals('FOO.BAR', array('Va\lue')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]A%s[SLASH]Z"))',
                    'Va[DBLSLASH]lue'
                )
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::if()->listEquals('FOO.BAR', array('6 + 4 * 3')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]A%s[SLASH]Z"))',
                    '6 [SLASH]+ 4 [SLASH]* 3'
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::if()->listEquals('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#if($map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]AFoo[SLASH]Z") || $map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]ABar[SLASH]Z"))'
            ),
            array(
                'label' => 'With quotes in search term',
                'mailcode' => Mailcode_Factory::if()->listEquals('FOO.BAR', array('Value, "weird" huh?')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)[SLASH]A%s[SLASH]Z"))',
                    'Value, [SLASH]"weird[SLASH]" huh[SLASH]?'
                )
            )
        );
        
        $this->runCommands($tests);
    }
}
