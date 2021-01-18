<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_CommentTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $template = 
        '[NL]#**[NL]'.
        '  %s[NL]'.
        '*#[NL]';
        
        $tests = array(
            array(
                'label' => 'Without quotes',
                'mailcode' => Mailcode_Factory::misc()->comment('Someone here'),
                'expected' => sprintf($template, 'Someone here')
            ),
            array(
                'label' => 'With quotes',
                'mailcode' => Mailcode_Factory::misc()->comment('Someone "is quoted" here'),
                'expected' => sprintf($template, 'Someone "is quoted" here')
            ),
            array(
                'label' => 'With special characters',
                'mailcode' => Mailcode_Factory::misc()->comment('insensitive: $FOOBAR (4*5/lopos)'),
                'expected' => sprintf($template, 'insensitive: $FOOBAR (4*5/lopos)')
            )
        );
        
        $this->runCommands($tests);
    }
}
