<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class IfEndsWithTests extends HubLTestCase
{
    public function test_endsWith(): void
    {
        $tests = array(
            array(
                'label' => 'Ends-with, case-sensitive, 6-char suffix',
                'mailcode' => Mailcode_Factory::if()->endsWith('FOO', 'suffix'),
                'expected' => '{% if foo[-6:] == "suffix" %}'
            ),
            array(
                'label' => 'Ends-with, single-char edge case (n=1)',
                'mailcode' => Mailcode_Factory::if()->endsWith('FOO', 'x'),
                'expected' => '{% if foo[-1:] == "x" %}'
            ),
            array(
                'label' => 'Ends-with, case-insensitive (var|lower, term lowercased)',
                'mailcode' => Mailcode_Factory::if()->endsWith('FOO', 'SUFFIX', true),
                'expected' => '{% if foo|lower[-6:] == "suffix" %}'
            ),
        );

        $this->runCommands($tests);
    }
}
