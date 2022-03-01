<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
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
            
            $expected = str_replace(
                array('[SLASH]', '[DBLSLASH]', '[FOURSLASH]', '[NL]'), 
                array('\\', '\\\\', '\\\\\\\\', PHP_EOL), 
                $test['expected']
            );
            
            $this->assertEquals($expected, $result, $test['label']);
        }
    }

    protected function translateCommand(Mailcode_Commands_Command $command) : string
    {
        return $this->translator
            ->createSyntax('ApacheVelocity')
            ->translateCommand($command);
    }
}
