<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowDate;

final class Translator_Velocity_ShowDateTests extends VelocityTestCase
{
    public function test_translateCommand(): void
    {
        $defaultFormat = Mailcode_Translator_Syntax_ApacheVelocity_ShowDate::DEFAULT_INTERNAL_FORMAT;

        $tests = array(
            array(
                'label' => 'Show date, default format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("yyyy/MM/dd").zone("UTC")}'
            ),
            array(
                'label' => 'Show date, german format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy H:m:s").zone("UTC")}'
            ),
            array(
                'label' => 'Show date, short year format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.y'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yy").zone("UTC")}'
            ),
            array(
                'label' => 'With URL encoding',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.y')->setURLEncoding(true),
                'expected' => '${esc.url($time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yy").zone("UTC"))}'
            ),
            array(
                'label' => 'Show date, german format, US timezone',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s', '"US/Eastern"'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy H:m:s").zone("US/Eastern")}'
            ),
            array(
                'label' => 'Show date, german format, US timezone',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s', 'US/Eastern'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy H:m:s").zone("US/Eastern")}'
            ),
            array(
                'label' => 'Show date, german format, variable timezone',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s', null, '$FOO.TIMEZONE'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy H:m:s").zone($FOO.TIMEZONE)}'
            ),
            array(
                'label' => 'Show date with milliseconds and time zone format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s v e'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy H:m:s SSS XXX").zone("UTC")}'
            )
        );

        $this->runCommands($tests);
    }

    public function test_setInternalFormat(): void
    {
        $var = Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y');

        $var->setTranslationParam('internal_format', 'yyyy-MM-dd');

        $syntax = $this->translator->createSyntax('ApacheVelocity');

        $result = $syntax->translateCommand($var);

        $this->assertEquals('${time.input("yyyy-MM-dd", $FOO.BAR).output("dd.MM.yyyy").zone("UTC")}', $result);
    }
}
