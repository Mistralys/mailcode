<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ElseIfBeginsWithTests extends HubLTestCase
{
    public function test_beginsWith(): void
    {
        $tests = array(
            array(
                'label' => 'ElseIf begins-with, case-sensitive',
                'mailcode' => Mailcode_Factory::elseIf()->beginsWith('VAR', 'pre'),
                'expected' => '{% elif var is string_startingwith "pre" %}'
            ),
            array(
                'label' => 'ElseIf begins-with, case-insensitive (var|lower, term lowercased)',
                'mailcode' => Mailcode_Factory::elseIf()->beginsWith('VAR', 'PRE', true),
                'expected' => '{% elif var|lower is string_startingwith "pre" %}'
            ),
        );

        $this->runCommands($tests);
    }
}
