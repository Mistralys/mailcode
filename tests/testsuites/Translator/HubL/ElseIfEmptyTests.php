<?php

declare(strict_types=1);

namespace testsuites\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;
use MailcodeTestClasses\VelocityTestCase;

final class ElseIfEmptyTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::elseIf()->empty('FOO.BAR'),
                'expected' => '{% elif !foo.bar %}'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::elseIf()->notEmpty('FOO.BAR'),
                'expected' => '{% elif foo.bar %}'
            )
        );

        $this->runCommands($tests);
    }
}
