<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ElseIfBiggerThanTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Integer value',
                'mailcode' => Mailcode_Factory::elseIf()->biggerThan('FOO.BAR', '100'),
                'expected' => <<<'EOD'
#elseif($numeric.toNumber($FOO.BAR) > 100)
EOD
            ),
            array(
                'label' => 'Value with comma',
                'mailcode' => Mailcode_Factory::elseIf()->biggerThan('FOO.BAR', '45,12'),
                'expected' => <<<'EOD'
#elseif($numeric.toNumber($FOO.BAR) > 45.12)
EOD

            ),
            array(
                'label' => 'Value with dot',
                'mailcode' => Mailcode_Factory::elseIf()->biggerThan('FOO.BAR', '45.12'),
                'expected' => <<<'EOD'
#elseif($numeric.toNumber($FOO.BAR) > 45.12)
EOD
            )
        );

        $this->runCommands($tests);
    }
}
