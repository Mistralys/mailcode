<?php

declare(strict_types=1);

namespace testsuites\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

final class ShowVariableTests extends HubLTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Show variable',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR'),
                'expected' => '{{ foo.bar }}'
            ),
            array(
                'label' => 'Show variable, URL encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setURLEncoding(),
                'expected' => '{{ foo.bar|urlencode }}'
            ),
            array(
                'label' => 'Show variable, URL decoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setURLDecoding(),
                'expected' => '{{ foo.bar|urldecode }}'
            ),
            array(
                'label' => 'Show variable, IDN encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNEncoding(),
                'expected' => '{{ foo.bar }}'
            ),
            array(
                'label' => 'Show variable, IDN decoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNDecoding(),
                'expected' => '{{ foo.bar }}'
            ),
            array(
                'label' => 'Show variable, multiple encodings, decryption',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->enableDecryption()
                    ->setIDNEncoding()
                    ->setURLEncoding(),
                'expected' => '{{ foo.bar|urlencode }}'
            ),
            array(
                'label' => 'Show variable, multiple encodings, custom decryption',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->enableDecryption('barfoo')
                    ->setIDNEncoding()
                    ->setURLEncoding(),
                'expected' => '{{ foo.bar|urlencode }}'
            )
        );

        $this->runCommands($tests);
    }
}
