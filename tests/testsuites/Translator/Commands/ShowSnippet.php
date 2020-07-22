<?php

use Mailcode\Mailcode_Factory;

final class Translator_Velocity_ShowSnippetTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Show snippet',
                'mailcode' => Mailcode_Factory::showSnippet('$snippetname'),
                'expected' => '${snippetname.replaceAll($esc.newline, "<br/>")}'
            )
        );
        
        $this->runCommands($tests);
    }
}
