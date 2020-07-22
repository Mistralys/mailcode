<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator;

abstract class VelocityTestCase extends MailcodeTestCase
{
    /**
     * @var Mailcode_Translator
     */
    protected $translator;
    
    protected function setUp() : void
    {
        $this->translator = Mailcode::create()->createTranslator();
    }
    
    protected function runCommands(array $tests) : void
    {
        $syntax = $this->translator->createSyntax('ApacheVelocity');
        
        foreach($tests as $test)
        {
            try
            {
                $result = $syntax->translateCommand($test['mailcode']);
            }
            catch(Mailcode_Exception $e)
            {
                $this->fail('Exception triggered: '.$e->getMessage().' | '.$e->getDetails());
            }
            
            $this->assertEquals($test['expected'], $result, $test['label']);
        }
    }
}
