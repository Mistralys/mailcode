<?php

use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;

final class Factory_FactoryTests extends MailcodeTestCase
{
    public function test_setVar()
    {
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => Mailcode_Factory::setVar('VAR.NAME', 'Some text')
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => Mailcode_Factory::setVar('$VAR.NAME', 'Some text')
            ),
            array(
                'label' => 'Unquoted params',
                'cmd' => Mailcode_Factory::setVar('$VAR.NAME', '6 + 2', false)
            )
        );
        
        // this is sufficient, since a factory call would throw an exception if anything is wrong
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_setVar_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::setVar('$FOO.BAR', 'Some text', false);
    }
    
    public function test_showVar()
    {
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => Mailcode_Factory::showVar('VAR.NAME')
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => Mailcode_Factory::showVar('$VAR.NAME')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_showVar_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::showVar('0INVALIDVAR');
    }
    
    public function test_showSnippet()
    {
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => Mailcode_Factory::showSnippet('snippet_name')
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => Mailcode_Factory::showSnippet('$snippet_name')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_showSnippet_error()
    {
        $this->expectException(Mailcode_Factory_Exception::class);
        
        Mailcode_Factory::showSnippet('0invalid_var');
    }
    
    public function test_elseIf()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::elseIf('$FOO.BAR == "Value"')
            ),
            array(
                'label' => 'Arithmetic operation',
                'cmd' => Mailcode_Factory::elseIf('6 * 2 == 78')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_elseIfVariable()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::elseIfVar('FOO.BAR', '==', 'Some text', true)
            ),
            array(
                'label' => 'Arithmetic operation, greater than',
                'cmd' => Mailcode_Factory::elseIfVar('$FOO.BAR', '>', '6 * 2')
            ),
            array(
                'label' => 'Arithmetic operation, smaller than',
                'cmd' => Mailcode_Factory::elseIfVar('$FOO.BAR', '<', '14.56')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_elseIfVariableEquals()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::elseIfVarEquals('FOO.BAR', 'Some text', true)
            ),
            array(
                'label' => 'Arithmetic operation',
                'cmd' => Mailcode_Factory::elseIfVarEquals('$FOO.BAR', '6 * 2')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_if()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::if('$FOO.BAR == "Value"')
            ),
            array(
                'label' => 'Arithmetic operation',
                'cmd' => Mailcode_Factory::if('6 * 2 == 78')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_ifVariable()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::ifVar('FOO.BAR', '==', 'Some text', true)
            ),
            array(
                'label' => 'Arithmetic operation, greater than',
                'cmd' => Mailcode_Factory::ifVar('$FOO.BAR', '>', '6 * 2')
            ),
            array(
                'label' => 'Arithmetic operation, smaller than',
                'cmd' => Mailcode_Factory::ifVar('$FOO.BAR', '<', '14.56')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_ifVariableEquals()
    {
        $tests = array(
            array(
                'label' => 'Variable string comparison',
                'cmd' => Mailcode_Factory::ifVarEquals('FOO.BAR', 'Some text', true)
            ),
            array(
                'label' => 'Arithmetic operation',
                'cmd' => Mailcode_Factory::ifVarEquals('$FOO.BAR', '6 * 2')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_filterVariableName()
    {
        $var = Mailcode_Factory::showVar('     $FOO   .     BAR    ');
        
        $this->assertTrue($var->isValid());
        $this->assertSame('{showvar: $FOO.BAR}', $var->getNormalized());
    }
    
    public function test_ifContains()
    {
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => Mailcode_Factory::ifContains('FOO.BAR', 'Value')
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => Mailcode_Factory::ifContains('$VAR.NAME', 'Value')
            ),
            array(
                'label' => 'Search for number',
                'cmd' => Mailcode_Factory::ifContains('$VAR.NAME', '64')
            ),
            array(
                'label' => 'Search for text with quotes',
                'cmd' => Mailcode_Factory::ifContains('$VAR.NAME', 'It\'s a "weird" foo.')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
    
    public function test_elseIfContains()
    {
        $tests = array(
            array(
                'label' => 'Variable name without $',
                'cmd' => Mailcode_Factory::elseIfContains('FOO.BAR', 'Value')
            ),
            array(
                'label' => 'Variable name with $',
                'cmd' => Mailcode_Factory::elseIfContains('$VAR.NAME', 'Value')
            ),
            array(
                'label' => 'Search for number',
                'cmd' => Mailcode_Factory::elseIfContains('$VAR.NAME', '64')
            ),
            array(
                'label' => 'Search for text with quotes',
                'cmd' => Mailcode_Factory::elseIfContains('$VAR.NAME', 'It\'s a "weird" foo.')
            )
        );
        
        $this->addToAssertionCount(count($tests));
    }
}
