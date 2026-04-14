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
}
