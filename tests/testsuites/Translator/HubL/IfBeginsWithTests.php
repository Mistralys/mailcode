<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class IfBeginsWithTests extends HubLTestCase
{
    public function test_beginsWith(): void
    {
        $tests = array(
            array(
                'label' => 'Begins-with, case-sensitive',
                'mailcode' => Mailcode_Factory::if()->beginsWith('VAR', 'pre'),
                'expected' => '{% if var is string_startingwith "pre" %}'
            ),
            array(
                'label' => 'Begins-with, case-insensitive (var|lower, term lowercased)',
                'mailcode' => Mailcode_Factory::if()->beginsWith('VAR', 'PRE', true),
                'expected' => '{% if var|lower is string_startingwith "pre" %}'
            ),
            array(
                'label' => 'ElseIf begins-with uses shared AbstractIfBase',
                'mailcode' => Mailcode_Factory::elseIf()->beginsWith('VAR', 'pre'),
                'expected' => '{% elif var is string_startingwith "pre" %}'
            ),
        );

        $this->runCommands($tests);
    }

    public function test_endsWith(): void
    {
        $tests = array(
            array(
                'label' => 'Ends-with, case-sensitive (n=6)',
                'mailcode' => Mailcode_Factory::if()->endsWith('VAR', 'suffix'),
                'expected' => '{% if var[-6:] == "suffix" %}'
            ),
            array(
                'label' => 'Ends-with, single char edge case (n=1)',
                'mailcode' => Mailcode_Factory::if()->endsWith('VAR', 'x'),
                'expected' => '{% if var[-1:] == "x" %}'
            ),
            array(
                'label' => 'Ends-with, case-insensitive (var|lower, term lowercased)',
                'mailcode' => Mailcode_Factory::if()->endsWith('VAR', 'SUF', true),
                'expected' => '{% if var|lower[-3:] == "suf" %}'
            ),
            array(
                'label' => 'ElseIf ends-with uses shared AbstractIfBase',
                'mailcode' => Mailcode_Factory::elseIf()->endsWith('VAR', 'suffix'),
                'expected' => '{% elif var[-6:] == "suffix" %}'
            ),
        );

        $this->runCommands($tests);
    }
}
