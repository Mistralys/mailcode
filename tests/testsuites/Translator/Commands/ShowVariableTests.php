<?php

declare(strict_types=1);

namespace testsuites\Translator\Commands;

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable;
use VelocityTestCase;

/**
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable
 */
final class ShowVariableTests extends VelocityTestCase
{
    public function test_translateCommand() : void
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
                'expected' => '${esc.idn($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, IDN decoded',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNDecoding(true),
                'expected' => '${esc.unidn($FOO.BAR)}'
            ),
            array(
                'label' => 'Show variable, multiple encodings',
                'mailcode' => Mailcode_Factory::show()
                    ->var('FOO.BAR')
                    ->setIDNEncoding(true)
                    ->setURLEncoding(true),
                'expected' => '${esc.url(${esc.idn($FOO.BAR)})}'
            )
        );
        
        $this->runCommands($tests);
    }
}
