<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class BreakTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Break',
                'mailcode' => Mailcode_Factory::misc()->break(),
                'expected' => '#{break}'
            )
        );
        
        $this->runCommands($tests);
    }
}
