<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfContainsTests extends VelocityTestCase
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
                'label' => 'If contains',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Va\lue'),
                'expected' => sprintf(
                    '#if($FOO.BAR.matches("(?s)%s"))',
                    'Va[FOURSLASH]lue'
                )
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => sprintf(
                    '#if($FOO.BAR.matches("(?s)%s"))',
                    addslashes('6 [DBLSLASH]+ 4 [DBLSLASH]* 3')
                )
            ),
            array(
                'label' => 'Several search terms',
                'mailcode' => Mailcode_Factory::ifContainsAny('FOO.BAR', array('Foo', 'Bar')),
                'expected' => '#if($FOO.BAR.matches("(?s)Foo") || $FOO.BAR.matches("(?s)Bar"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Value, "quoted" yeah?'),
                'expected' => sprintf(
                    '#if($FOO.BAR.matches("(?s)%s"))',
                    'Value, [SLASH]"quoted[SLASH]" yeah[DBLSLASH]?'
                )
            )
        );
        
        $this->runCommands($tests);
    }
}
