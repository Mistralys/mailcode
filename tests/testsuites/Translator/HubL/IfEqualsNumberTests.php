<?php

declare(strict_types=1);

namespace MailCodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;
use MailcodeTestClasses\VelocityTestCase;

final class IfEqualsNumberTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Integer value',
                'mailcode' => Mailcode_Factory::if()->varEqualsNumber('FOO.BAR', '100'),
                'expected' => <<<'EOD'
{% if foo.bar|float == 100 %}
EOD
            ),
            array(
                'label' => 'Value with comma',
                'mailcode' => Mailcode_Factory::if()->varEqualsNumber('FOO.BAR', '45,12'),
                'expected' => <<<'EOD'
{% if foo.bar|float == 45.12 %}
EOD
            ),
            array(
                'label' => 'Value with dot',
                'mailcode' => Mailcode_Factory::if()->varEqualsNumber('FOO.BAR', '45.12'),
                'expected' => <<<'EOD'
{% if foo.bar|float == 45.12 %}
EOD
            )
        );

        $this->runCommands($tests);
    }
}
