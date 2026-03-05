<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class IfContainsTests extends HubLTestCase
{
    public function test_contains(): void
    {
        $tests = array(
            array(
                'label' => 'Contains single term, case-sensitive',
                'mailcode' => Mailcode_Factory::if()->contains('VAR', array('term')),
                'expected' => '{% if "term" in var %}'
            ),
            array(
                'label' => 'Contains single term, case-insensitive (term lowercased, var|lower)',
                'mailcode' => Mailcode_Factory::if()->contains('VAR', array('Term'), true),
                'expected' => '{% if "term" in var|lower %}'
            ),
            array(
                'label' => 'Contains multiple terms, OR logic',
                'mailcode' => Mailcode_Factory::if()->contains('VAR', array('t1', 't2')),
                'expected' => '{% if "t1" in var or "t2" in var %}'
            ),
            array(
                'label' => 'Not-contains single term',
                'mailcode' => Mailcode_Factory::if()->notContains('VAR', array('term')),
                'expected' => '{% if "term" not in var %}'
            ),
            array(
                'label' => 'Not-contains multiple terms, AND logic',
                'mailcode' => Mailcode_Factory::if()->notContains('VAR', array('t1', 't2')),
                'expected' => '{% if "t1" not in var and "t2" not in var %}'
            ),
            array(
                'label' => 'List-contains returns not-implemented stub',
                'mailcode' => Mailcode_Factory::if()->listContains('LIST.ITEM', array('term')),
                'expected' => '{% if {# ! if commands are not fully implemented ! #} %}'
            ),
        );

        $this->runCommands($tests);
    }

    public function test_elseif_contains(): void
    {
        $tests = array(
            array(
                'label' => 'ElseIf contains uses shared AbstractIfBase',
                'mailcode' => Mailcode_Factory::elseIf()->contains('VAR', array('term')),
                'expected' => '{% elif "term" in var %}'
            ),
            array(
                'label' => 'ElseIf not-contains',
                'mailcode' => Mailcode_Factory::elseIf()->notContains('VAR', array('term')),
                'expected' => '{% elif "term" not in var %}'
            ),
        );

        $this->runCommands($tests);
    }
}
