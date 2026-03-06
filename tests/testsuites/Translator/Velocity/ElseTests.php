<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class ElseTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Else',
                'mailcode' => Mailcode_Factory::elseIf()->else(),
                'expected' => '#{else}'
            )
        );
        
        $this->runCommands($tests);
    }
}
