<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowDate;

final class Translator_Velocity_ShowDateTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $defaultFormat = Mailcode_Translator_Syntax_ApacheVelocity_ShowDate::DEFAULT_INTERNAL_FORMAT;

        $tests = array(
            array(
                'label' => 'Show date, default format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR'),
                'expected' => '${date.format("yyyy/MM/dd", $date.toDate("'.$defaultFormat.'", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, german format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.Y H:i:s'),
                'expected' => '${date.format("dd.MM.yyyy H:m:s", $date.toDate("'.$defaultFormat.'", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, short year format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.y'),
                'expected' => '${date.format("dd.MM.yy", $date.toDate("'.$defaultFormat.'", $FOO.BAR))}'
            )
        );
        
        $this->runCommands($tests);
    }

    public function test_setInternalFormat() : void
    {
        $var = Mailcode_Factory::showDate('FOO.BAR', 'd.m.Y');

        $var->setTranslationParam('internal_format', 'yyyy-MM-dd');

        $syntax = $this->translator->createSyntax('ApacheVelocity');

        $result = $syntax->translateCommand($var);

        $this->assertEquals('${date.format("dd.MM.yyyy", $date.toDate("yyyy-MM-dd", $FOO.BAR))}', $result);
    }
}
