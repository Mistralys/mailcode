<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ShowPriceTests extends VelocityTestCase
{
    public function test_translateCommand(): void
    {
        $tests = array(
            array(
                'label' => 'Default',
                'mailcode' => Mailcode_Factory::show()
                    ->price('FOO.BAR'),
                'expected' => <<<'EOD'
${money.amount($FOO.BAR, '.').group(',').unit('USD', 'US').separator(' ')}
EOD
            ),
            array(
                'label' => 'With absolute number',
                'mailcode' => Mailcode_Factory::show()
                    ->price('FOO.BAR', true),
                'expected' => <<<'EOD'
${money.amount(${numeric.abs($FOO.BAR)}, '.').group(',').unit('USD', 'US').separator(' ')}
EOD
            ),
            array(
                'label' => 'With currency symbol instead of name',
                'mailcode' => Mailcode_Factory::show()
                    ->price('FOO.BAR', false, false),
                'expected' => <<<'EOD'
${money.amount($FOO.BAR, '.').group(',').unit('$', 'US').separator(' ')}
EOD
            )
        );

        $this->runCommands($tests);
    }
}
