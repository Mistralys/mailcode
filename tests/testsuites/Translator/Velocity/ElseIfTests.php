<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class ElseIfTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Else if',
                'mailcode' => Mailcode_Factory::elseIf()->elseIf('$FOO.BAR + 2 == 45'),
                'expected' => '#elseif($FOO.BAR + 2 == 45)'
            ),
            array(
                'label' => 'If with Velocity syntax',
                'mailcode' => Mailcode_Factory::elseIf()->elseIf('$FOO.BAR.urldecode().match(".*[?].*")'),
                'expected' => '#elseif($FOO.BAR.urldecode().match(".*[?].*"))'
            )
        );
        
        $this->runCommands($tests);
    }
}
