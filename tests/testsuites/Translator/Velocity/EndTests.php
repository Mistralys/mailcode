<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class EndTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'End',
                'mailcode' => Mailcode_Factory::misc()->end(),
                'expected' => '#{end}'
            )
        );
        
        $this->runCommands($tests);
    }
}
