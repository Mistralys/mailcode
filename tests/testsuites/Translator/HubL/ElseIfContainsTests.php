<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ElseIfContainsTests extends HubLTestCase
{
    public function test_contains(): void
    {
        $tests = array(
            array(
                'label' => 'ElseIf contains single term, case-sensitive',
                'mailcode' => Mailcode_Factory::elseIf()->contains('VAR', array('term')),
                'expected' => '{% elif "term" in var %}'
            ),
            array(
                'label' => 'ElseIf contains single term, case-insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->contains('VAR', array('Term'), true),
                'expected' => '{% elif "term" in var|lower %}'
            ),
            array(
                'label' => 'ElseIf contains multiple terms, OR logic',
                'mailcode' => Mailcode_Factory::elseIf()->contains('VAR', array('t1', 't2')),
                'expected' => '{% elif "t1" in var or "t2" in var %}'
            ),
        );

        $this->runCommands($tests);
    }
}
