<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class IfSmallerThanTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Integer value',
                'mailcode' => Mailcode_Factory::if()->smallerThan('FOO.BAR', '100'),
                'expected' => <<<'EOD'
#if($numeric.toNumber($FOO.BAR) < 100)
EOD
            ),
            array(
                'label' => 'Value with comma',
                'mailcode' => Mailcode_Factory::if()->smallerThan('FOO.BAR', '45,12'),
                'expected' => <<<'EOD'
#if($numeric.toNumber($FOO.BAR) < 45.12)
EOD
            ),
            array(
                'label' => 'Value with dot',
                'mailcode' => Mailcode_Factory::if()->smallerThan('FOO.BAR', '45.12'),
                'expected' => <<<'EOD'
#if($numeric.toNumber($FOO.BAR) < 45.12)
EOD
            )
        );

        $this->runCommands($tests);
    }
}
