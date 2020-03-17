<?php

use Mailcode\Mailcode_Factory;

final class Factory_RendererTests extends MailcodeTestCase
{
    public function test_setVar()
    {
        $renderer = Mailcode_Factory::createRenderer();
        
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => $renderer->setVarString('VAR.NAME', 'Some text'),
                'expected' => '{setvar: $VAR.NAME = "Some text"}'
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => $renderer->setVarString('$VAR.NAME', 'Some text'),
                'expected' => '{setvar: $VAR.NAME = "Some text"}'
            ),
            array(
                'label' => 'Unquoted params',
                'cmd' => Mailcode_Factory::setVar('$VAR.NAME', '6 + 2', false),
                'expected' => '{setvar: $VAR.NAME = 6 + 2}'
            )
        );
        
        foreach($tests as $test)
        {
            $this->assertEquals($test['expected'], $test['cmd'], $test['label']);
        }
    }
    
    public function test_showSnippet()
    {
        $renderer = Mailcode_Factory::createRenderer();
        
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => $renderer->showSnippet('snippet_name'),
                'expected' => '{showsnippet: $snippet_name}'
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => $renderer->showSnippet('$snippet_name'),
                'expected' => '{showsnippet: $snippet_name}'
            )
        );
        
        foreach($tests as $test)
        {
            $this->assertEquals($test['expected'], $test['cmd'], $test['label']);
        }
    }
}
