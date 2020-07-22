<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

final class Translator_ApacheVelocityTests extends VelocityTestCase
{
    public function test_logicKeywords()
    {
        $cmd = Mailcode_Factory::ifVar('FOO.BAR', '==', 20);
        $cmd->getLogicKeywords()->appendAND('$BARFOO == "Other value"', 'variable');
        
        $expected = '#if($FOO.BAR == 20 && $BARFOO == "Other value")';
        
        $syntax = $this->translator->createSyntax('ApacheVelocity');
        
        $result = $syntax->translateCommand($cmd);
        
        $this->assertEquals($expected, $result);
    }
    
    public function test_translateSafeguard()
    {
        $syntax = $this->translator->createSyntax('ApacheVelocity');
        
        $subject = '
{setvar: $CUSTOMER.CUSTOMER_ID = "42"}

{showvar: $CUSTOMER.CUSTOMER_ID}

{if variable: $FOO.BAR == "NOPE"}
    Some text here in the IF command.
{elseif: 6 + 2 == 5}
    You will never see me.
{else}
    Lucky you!
{end}
';

        $expected = '
#set($CUSTOMER.CUSTOMER_ID = "42")

${CUSTOMER.CUSTOMER_ID}

#if($FOO.BAR == "NOPE")
    Some text here in the IF command.
#elseif(6 + 2 == 5)
    You will never see me.
#{else}
    Lucky you!
#{end}
';
        
        $safeguard = Mailcode::create()->createSafeguard($subject);
        
        $result = $syntax->translateSafeguard($safeguard);
      
        $this->assertEquals($expected, $result);
    }
}
