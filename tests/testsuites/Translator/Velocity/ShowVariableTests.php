<?php

declare(strict_types=1);

namespace testsuites\Translator\Commands;

use Mailcode\Mailcode_Factory;
use Mailcode\Translator\Syntax\ApacheVelocity\ShowVariableTranslation;
use VelocityTestCase;

/**
 * @see \Mailcode\Translator\Syntax\ApacheVelocity\ShowVariableTranslation
 */
final class ShowVariableTests extends VelocityTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Show variable',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR'),
                'expected' => '${FOO.BAR}'
            ),
            array(
                'label' => 'Show variable, URL encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setURLEncoding(true),
                'expected' => '${esc.url($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, URL decoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setURLDecoding(true),
                'expected' => '${esc.unurl($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, IDN encoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNEncoding(true),
                'expected' => '${text.idn($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, IDN decoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNDecoding(true),
                'expected' => '${text.unidn($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, multiple encodings, decryption',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->enableDecryption()
                    ->setIDNEncoding(true)
                    ->setURLEncoding(),
                'expected' => '${esc.url(${text.idn(${text.decrypt($FOO.BAR, "default")})})}'
            ),
            array(
                'label' => 'Show variable, multiple encodings, custom decryption',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->enableDecryption('barfoo')
                    ->setIDNEncoding(true)
                    ->setURLEncoding(),
                'expected' => '${esc.url(${text.idn(${text.decrypt($FOO.BAR, "barfoo")})})}'
            )
        );

        $this->runCommands($tests);
    }
}
