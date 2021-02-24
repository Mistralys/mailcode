<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfListContainsTests extends VelocityTestCase
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
                'label' => 'If list contains',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('Value')),
                'expected' => '#if($map.hasElement($FOO.list(), "BAR", "(?s)Value"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('Va\lue')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)%s"))',
                    'Va[DBLSLASH]lue'
                )
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('6 + 4 * 3')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)%s"))',
                    '6 [SLASH]+ 4 [SLASH]* 3'
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#if($map.hasElement($FOO.list(), "BAR", "(?s)Foo") || $map.hasElement($FOO.list(), "BAR", "(?s)Bar"))'
            ),
            array(
                'label' => 'With quotes in search term',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('Value, "weird" huh?')),
                'expected' => sprintf(
                    '#if($map.hasElement($FOO.list(), "BAR", "(?s)%s"))',
                    'Value, [SLASH]"weird[SLASH]" huh[SLASH]?'
                )
            ),
            array(
                'label' => 'With regex mode enabled',
                'mailcode' => Mailcode_Factory::if()->listContains('FOO.BAR', array('.*Foo.*'), false, true),
                'expected' => '#if($map.hasElement($FOO.list(), "BAR", "(?s).*Foo.*"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
