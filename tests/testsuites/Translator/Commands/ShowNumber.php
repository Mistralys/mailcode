<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowNumberTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Default format (1000.00)',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR'),
                'expected' => <<<'EOD'
${number.format('#.##', $number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US'), 'en_US')}
EOD
            ),
            array(
                'label' => 'No decimals',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR', '1000'),
                'expected' => <<<'EOD'
${number.format('#', $number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US'), 'en_US')}
EOD
            ),
            array(
                'label' => 'No decimals, thousands separator',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR', '1,000'),
                'expected' => <<<'EOD'
${number.format('#,###', $number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US'), 'en_US')}
EOD
            ),
            array(
                'label' => 'German format (1.000,00)',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR', '1.000,00'),
                'expected' => <<<'EOD'
${number.format('#,###.##', $number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US'), 'de_DE')}
EOD
            ),
            array(
                'label' => 'French format (1 000,00)',
                'mailcode' => Mailcode_Factory::showNumber('FOO.BAR', '1 000,00'),
                'expected' => <<<'EOD'
${number.format('#,###.##', $number.toNumber('#.####', $FOO.BAR.replace(',', '.'), 'en_US'), 'fr_FR')}
EOD
            )
        );
        
        $this->runCommands($tests);
    }
}
