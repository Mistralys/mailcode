<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class IfNotContainsTests extends HubLTestCase
{
    public function test_notContains(): void
    {
        $tests = array(
            array(
                'label' => 'Not-contains single term, case-sensitive',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO', array('bar')),
                'expected' => '{% if "bar" not in foo %}'
            ),
            array(
                'label' => 'Not-contains single term, case-insensitive (term lowercased, var|lower)',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO', array('BAR'), true),
                'expected' => '{% if "bar" not in foo|lower %}'
            ),
            array(
                'label' => 'Not-contains multiple terms, AND logic',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO', array('a', 'b')),
                'expected' => '{% if "a" not in foo and "b" not in foo %}'
            ),
            array(
                'label' => 'Not-contains multiple terms, case-insensitive',
                'mailcode' => Mailcode_Factory::if()->notContains('FOO', array('A', 'B'), true),
                'expected' => '{% if "a" not in foo|lower and "b" not in foo|lower %}'
            ),
        );

        $this->runCommands($tests);
    }
}
