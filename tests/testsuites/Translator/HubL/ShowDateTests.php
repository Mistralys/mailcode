<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ShowDateTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Show date with variable, default format (Y/m/d)',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR'),
                'expected' => '{{ foo.bar|format_datetime("yyyy/MM/dd") }}'
            ),
            array(
                'label' => 'Show date with variable, d/m/Y format',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y'),
                'expected' => '{{ foo.bar|format_datetime("dd/MM/yyyy") }}'
            ),
            array(
                'label' => 'Show date with timezone string literal',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y', 'Europe/Paris'),
                'expected' => '{{ foo.bar|format_datetime("dd/MM/yyyy", "Europe/Paris") }}'
            ),
            array(
                'label' => 'Show date with timezone variable',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y', null, '$FOO.TZ'),
                'expected' => '{{ foo.bar|format_datetime("dd/MM/yyyy", foo.tz) }}'
            ),
            array(
                'label' => 'Show current date without variable (local_dt)',
                'mailcode' => Mailcode_Factory::show()->dateNow('d/m/Y'),
                'expected' => '{{ local_dt|format_datetime("dd/MM/yyyy") }}'
            ),
            array(
                'label' => 'Show date with URL encoding',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y')->setURLEncoding(true),
                'expected' => '{{ foo.bar|format_datetime("dd/MM/yyyy")|urlencode }}'
            ),
            array(
                'label' => 'PHP-to-LDML: Y-m-d H:i:s',
                'mailcode' => Mailcode_Factory::show()->date('FOO.BAR', 'Y-m-d H:i:s'),
                'expected' => '{{ foo.bar|format_datetime("yyyy-MM-dd HH:mm:ss") }}'
            ),
        );

        $this->runCommands($tests);
    }

    public function test_internalFormat_wrapsInStringConditional(): void
    {
        $cmd = Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y');
        $cmd->setTranslationParam('internal_format', "yyyy-MM-dd'T'HH:mm:ss.SSSXXX");

        $result = $this->translator->createHubL()->translateCommand($cmd);

        $this->assertSame(
            '{% if foo.bar is string %}{{ foo.bar|strtotime("yyyy-MM-dd\'T\'HH:mm:ss.SSSXXX")|format_datetime("dd/MM/yyyy") }}{% else %}{{ foo.bar|format_datetime("dd/MM/yyyy") }}{% endif %}',
            $result
        );
    }

    public function test_internalFormat_withTimezone(): void
    {
        $cmd = Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y', 'Europe/Paris');
        $cmd->setTranslationParam('internal_format', 'yyyy-MM-dd');

        $result = $this->translator->createHubL()->translateCommand($cmd);

        $this->assertSame(
            '{% if foo.bar is string %}{{ foo.bar|strtotime("yyyy-MM-dd")|format_datetime("dd/MM/yyyy", "Europe/Paris") }}{% else %}{{ foo.bar|format_datetime("dd/MM/yyyy", "Europe/Paris") }}{% endif %}',
            $result
        );
    }

    public function test_withoutInternalFormat_noConditional(): void
    {
        $cmd = Mailcode_Factory::show()->date('FOO.BAR', 'd/m/Y');

        $result = $this->translator->createHubL()->translateCommand($cmd);

        $this->assertSame(
            '{{ foo.bar|format_datetime("dd/MM/yyyy") }}',
            $result
        );
    }
}
