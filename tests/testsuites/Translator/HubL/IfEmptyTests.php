<?php

declare(strict_types=1);

namespace MailCodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;
use MailcodeTestClasses\VelocityTestCase;

final class IfEmptyTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::if()->empty('FOO.BAR'),
                'expected' => '{% if !foo.bar %}'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::if()->notEmpty('FOO.BAR'),
                'expected' => '{% if foo.bar %}'
            )
        );

        $this->runCommands($tests);
    }
}
