<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Date_FormatInfo;
use Mailcode\Mailcode_Translator_Exception;
use Mailcode\Translator\Syntax\ApacheVelocity\ShowDateTranslation;
use MailcodeTestClasses\VelocityTestCase;

final class ShowDateTests extends VelocityTestCase
{
    public function test_translateCommand(): void
    {
        $defaultFormat = ShowDateTranslation::DEFAULT_INTERNAL_FORMAT;

        $tests = array(
            array(
                'label' => 'Show date, default format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("yyyy/MM/dd").zone("UTC")}'
            ),
            array(
                'label' => 'Show date, german format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy HH:mm:ss").zone("UTC")}'
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
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy HH:mm:ss").zone("US/Eastern")}'
            ),
            array(
                'label' => 'Show date, german format, US timezone',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s', 'US/Eastern'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy HH:mm:ss").zone("US/Eastern")}'
            ),
            array(
                'label' => 'Show date, german format, variable timezone',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s', null, '$FOO.TIMEZONE'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy HH:mm:ss").zone($FOO.TIMEZONE)}'
            ),
            array(
                'label' => 'Show date with milliseconds and time zone format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y H:i:s v e'),
                'expected' => '${time.input("' . $defaultFormat . '", $FOO.BAR).output("dd.MM.yyyy HH:mm:ss SSS XXX").zone("UTC")}'
            ),
            array(
                'label' => 'Show current date with milliseconds and time zone format',
                'mailcode' => Mailcode_Factory::show()->dateNow('d.m.Y H:i:s v e'),
                'expected' => '${date.get("dd.MM.yyyy HH:mm:ss SSS XXX").zone("UTC")}'
            ),
            array(
                'label' => 'Show current date with milliseconds and time zone format',
                'mailcode' => Mailcode_Factory::show()->dateNow('d.m.Y H:i:s v e', "Europe/Berlin"),
                'expected' => '${date.get("dd.MM.yyyy HH:mm:ss SSS XXX").zone("Europe/Berlin")}'
            )
        );

        $this->runCommands($tests);
    }

    public function test_setInternalFormat(): void
    {
        $var = Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y');

        $var->setTranslationParam('internal_format', 'yyyy-MM-dd');

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($var);

        $this->assertEquals('${time.input("yyyy-MM-dd", $FOO.BAR).output("dd.MM.yyyy").zone("UTC")}', $result);
    }

    public function test_formatConversions(): void
    {
        $syntax = $this->translator->createApacheVelocity();

        foreach (ShowDateTranslation::$charTable as $phpChar => $javaChar) {
            if ($phpChar === ' ') {
                continue;
            }

            $result = $syntax->translateCommand(Mailcode_Factory::show()->date('FOO.BAR', $phpChar));

            $this->assertStringContainsString($javaChar, $result, 'PHP Char: [' . $phpChar . ']');
        }
    }

    public function test_internalFormat_withBrackets_accepted(): void
    {
        $var = Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y');
        $var->setTranslationParam('internal_format', "yyyy-MM-dd['T'HH:mm:ss]");

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($var);

        $this->assertStringContainsString("yyyy-MM-dd['T'HH:mm:ss]", $result);
    }

    public function test_internalFormat_withoutBrackets_works(): void
    {
        $var = Mailcode_Factory::show()->date('FOO.BAR', 'd.m.Y');
        $var->setTranslationParam('internal_format', 'yyyy-MM-dd');

        $syntax = $this->translator->createApacheVelocity();
        $result = $syntax->translateCommand($var);

        $this->assertStringContainsString('yyyy-MM-dd', $result);
    }

    public function test_validateJavaFormat_withBrackets(): void
    {
        $result = Mailcode_Date_FormatInfo::validateJavaFormat("yyyy-MM-dd['T'HH:mm:ss]");
        $this->assertFalse($result->isValid());
        $this->assertSame(Mailcode_Date_FormatInfo::VALIDATION_JAVA_OPTIONAL_BRACKETS_NOT_SUPPORTED, $result->getCode());
    }

    public function test_validateJavaFormat_openBracketOnly(): void
    {
        $result = Mailcode_Date_FormatInfo::validateJavaFormat('yyyy-MM-dd[');
        $this->assertFalse($result->isValid());
    }

    public function test_validateJavaFormat_closeBracketOnly(): void
    {
        $result = Mailcode_Date_FormatInfo::validateJavaFormat('yyyy-MM-dd]');
        $this->assertFalse($result->isValid());
    }

    public function test_validateJavaFormat_cleanString(): void
    {
        $result = Mailcode_Date_FormatInfo::validateJavaFormat("yyyy-MM-dd'T'HH:mm:ss.SSSXXX");
        $this->assertTrue($result->isValid());
    }
}
