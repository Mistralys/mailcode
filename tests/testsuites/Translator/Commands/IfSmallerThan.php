<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_IfSmallerThanTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Integer value',
                'mailcode' => Mailcode_Factory::ifSmallerThan('FOO.BAR', '100'),
                'expected' => <<<'EOD'
#if($number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US') < 100)
EOD
            ),
            array(
                'label' => 'Value with comma',
                'mailcode' => Mailcode_Factory::ifSmallerThan('FOO.BAR', '45,12'),
                'expected' => <<<'EOD'
#if($number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US') < 45.12)
EOD
            ),
            array(
                'label' => 'Value with dot',
                'mailcode' => Mailcode_Factory::ifSmallerThan('FOO.BAR', '45.12'),
                'expected' => <<<'EOD'
#if($number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US') < 45.12)
EOD
            )
        );
        
        $this->runCommands($tests);
    }
}
