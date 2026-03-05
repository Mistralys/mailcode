<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ForTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Simple for loop',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP'),
                'expected' => '{% for loop in source %}'
            ),
            array(
                'label' => 'For loop with numeric break_at',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP', '13'),
                'expected' => '{% for loop in source[:13] %}'
            ),
            array(
                'label' => 'For loop with variable break_at (lowercase output)',
                'mailcode' => Mailcode_Factory::misc()->for('SOURCE', 'LOOP', '$FOO.BAR'),
                'expected' => '{% for loop in source[:foo.bar] %}'
            ),
            array(
                'label' => 'For loop variable names are lowercased',
                'mailcode' => Mailcode_Factory::misc()->for('MySource', 'MyRecord'),
                'expected' => '{% for myrecord in mysource %}'
            ),
        );

        $this->runCommands($tests);
    }
}
