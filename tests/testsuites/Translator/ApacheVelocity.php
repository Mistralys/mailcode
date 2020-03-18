<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Translator;
use Mailcode\Mailcode_Factory;

final class Translator_ApacheVelocityTests extends MailcodeTestCase
{
   /**
    * @var Mailcode_Translator
    */
    protected $translator;
    
    protected function setUp() : void
    {
        $this->translator = Mailcode::create()->createTranslator();
    }
    
    public function test_translateCommand()
    {
        $tests = array(
            array(
                'label' => 'Show variable',
                'mailcode' => Mailcode_Factory::showVar('FOO.BAR'),
                'expected' => '${FOO.BAR}'
            ),
            array(
                'label' => 'Set variable',
                'mailcode' => Mailcode_Factory::setVar('FOO.BAR', 'Value', true),
                'expected' => '#set($FOO.BAR = "Value")'
            ),
            array(
                'label' => 'If var equals string',
                'mailcode' => Mailcode_Factory::ifVarEqualsString('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR == "Value")'
            ),
            array(
                'label' => 'ElseIf var equals string',
                'mailcode' => Mailcode_Factory::elseIfVarEqualsString('FOO.BAR', 'Value'),
                'expected' => '#elseif($FOO.BAR == "Value")'
            ),
            array(
                'label' => 'Else',
                'mailcode' => Mailcode_Factory::else(),
                'expected' => '#{else}'
            ),
            array(
                'label' => 'End',
                'mailcode' => Mailcode_Factory::end(),
                'expected' => '#{end}'
            ),
            array(
                'label' => 'If contains',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Value'),
                'expected' => '#if($FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Va\lue'),
                'expected' => '#if($FOO.BAR.matches("(?s)Va\\\\lue"))'
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => '#if($FOO.BAR.matches("(?s)6 \+ 4 \* 3"))'
            )
        );
        
        $syntax = $this->translator->createSyntax('ApacheVelocity');
        
        foreach($tests as $test)
        {
            $result = $syntax->translateCommand($test['mailcode']);
            
            $this->assertEquals($test['expected'], $result);
        }
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
