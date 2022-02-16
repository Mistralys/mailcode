<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowNumberTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Default format (1000.00)',
                'mailcode' => Mailcode_Factory::show()->number('FOO.BAR'),
                'expected' => <<<'EOD'
${price.format($FOO.BAR, 2, '.', '')}
EOD
            ),
            array(
                'label' => 'No decimals',
                'mailcode' => Mailcode_Factory::show()->number('FOO.BAR', '1000'),
                'expected' => <<<'EOD'
${price.format($FOO.BAR, 0, '', '')}
EOD
            ),
            array(
                'label' => 'No decimals, thousands separator',
                'mailcode' => Mailcode_Factory::show()->number('FOO.BAR', '1,000'),
                'expected' => <<<'EOD'
${price.format($FOO.BAR, 0, '', ',')}
EOD
            ),
            array(
                'label' => 'German format (1.000,00)',
                'mailcode' => Mailcode_Factory::show()->number('FOO.BAR', '1.000,00'),
                'expected' => <<<'EOD'
${price.format($FOO.BAR, 2, ',', '.')}
EOD
            ),
            array(
                'label' => 'French format (1 000,00)',
                'mailcode' => Mailcode_Factory::show()->number('FOO.BAR', '1 000,00'),
                'expected' => <<<'EOD'
${price.format($FOO.BAR, 2, ',', ' ')}
EOD
            ),
            array(
                'label' => 'With absolute number',
                'mailcode' => Mailcode_Factory::show()
                    ->number('FOO.BAR', '1 000,00', true),
                'expected' => <<<'EOD'
${price.format(${price.abs($FOO.BAR)}, 2, ',', ' ')}
EOD
            )
        );
        
        $this->runCommands($tests);
    }
}
