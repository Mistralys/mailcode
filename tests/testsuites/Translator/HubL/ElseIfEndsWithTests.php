<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ElseIfEndsWithTests extends HubLTestCase
{
    public function test_endsWith(): void
    {
        $tests = array(
            array(
                'label' => 'ElseIf ends-with, case-sensitive, 6-char suffix',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('VAR', 'suffix'),
                'expected' => '{% elif var[-6:] == "suffix" %}'
            ),
            array(
                'label' => 'ElseIf ends-with, single-char edge case (n=1)',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('VAR', 'x'),
                'expected' => '{% elif var[-1:] == "x" %}'
            ),
            array(
                'label' => 'ElseIf ends-with, case-insensitive (var|lower, term lowercased)',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('VAR', 'SUF', true),
                'expected' => '{% elif var|lower[-3:] == "suf" %}'
            ),
        );

        $this->runCommands($tests);
    }
}
