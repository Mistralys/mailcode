<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class IfEndsWithTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'If ends with',
                'mailcode' => Mailcode_Factory::if()->endsWith('FOO.BAR', 'Search'),
                'expected' => '#if($StringUtils.endsWith($FOO.BAR, "Search"))'
            ),
            array(
                'label' => 'If ends with, case insensitive',
                'mailcode' => Mailcode_Factory::if()->endsWith('FOO.BAR', 'Search', true),
                'expected' => '#if($StringUtils.endsWithIgnoreCase($FOO.BAR, "Search"))'
            ),
        );
        
        $this->runCommands($tests);
    }
}
