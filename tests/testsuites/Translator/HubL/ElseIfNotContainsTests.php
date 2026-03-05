<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ElseIfNotContainsTests extends HubLTestCase
{
    public function test_notContains(): void
    {
        $tests = array(
            array(
                'label' => 'ElseIf not-contains single term, case-sensitive',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('VAR', array('term')),
                'expected' => '{% elif "term" not in var %}'
            ),
            array(
                'label' => 'ElseIf not-contains single term, case-insensitive',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('VAR', array('Term'), true),
                'expected' => '{% elif "term" not in var|lower %}'
            ),
            array(
                'label' => 'ElseIf not-contains multiple terms, AND logic',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('VAR', array('t1', 't2')),
                'expected' => '{% elif "t1" not in var and "t2" not in var %}'
            ),
        );

        $this->runCommands($tests);
    }
}
