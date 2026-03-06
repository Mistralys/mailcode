<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class IfEmptyTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::if()->empty('FOO.BAR'),
                'expected' => '#if($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::if()->notEmpty('FOO.BAR'),
                'expected' => '#if(!$StringUtils.isEmpty($FOO.BAR))'
            )
        );
        
        $this->runCommands($tests);
    }
}
