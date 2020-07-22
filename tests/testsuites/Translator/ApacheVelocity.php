<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Translator;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Exception;

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
                'label' => 'Show date, default format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR'),
                'expected' => '${date.format("yyyy/M/d", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, german format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.Y H:i:s'),
                'expected' => '${date.format("d.M.yyyy H:m:s", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show date, short year format',
                'mailcode' => Mailcode_Factory::showDate('FOO.BAR', 'd.m.y'),
                'expected' => '${date.format("d.M.yy", $date.toDate("yyyy-MM-dd HH:mm:ss.SSS", $FOO.BAR))}'
            ),
            array(
                'label' => 'Show snippet',
                'mailcode' => Mailcode_Factory::showSnippet('$snippetname'),
                'expected' => '${snippetname.replaceAll($esc.newline, "<br/>")}'
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
                'label' => 'ElseIf contains',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', 'Value'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)Value"))'
            ),
            array(
                'label' => 'If contains with slash',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', 'Va\lue'),
                'expected' => '#if($FOO.BAR.matches("(?s)Va\\\\lue"))'
            ),
            array(
                'label' => 'ElseIf contains with slash',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', 'Va\lue'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)Va\\\\lue"))'
            ),
            array(
                'label' => 'If contains with special characters',
                'mailcode' => Mailcode_Factory::ifContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => '#if($FOO.BAR.matches("(?s)6 \+ 4 \* 3"))'
            ),
            array(
                'label' => 'ElseIf contains with special characters',
                'mailcode' => Mailcode_Factory::elseIfContains('FOO.BAR', '6 + 4 * 3'),
                'expected' => '#elseif($FOO.BAR.matches("(?s)6 \+ 4 \* 3"))'
            ),
            array(
                'label' => 'If empty',
                'mailcode' => Mailcode_Factory::ifEmpty('FOO.BAR'),
                'expected' => '#if($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'ElseIf empty',
                'mailcode' => Mailcode_Factory::elseIfEmpty('FOO.BAR'),
                'expected' => '#elseif($StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'If not empty',
                'mailcode' => Mailcode_Factory::ifNotEmpty('FOO.BAR'),
                'expected' => '#if(!$StringUtils.isEmpty($FOO.BAR))'
            ),
            array(
                'label' => 'ElseIf not empty',
                'mailcode' => Mailcode_Factory::elseIfNotEmpty('FOO.BAR'),
                'expected' => '#elseif(!$StringUtils.isEmpty($FOO.BAR))'
            )
        );
        
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
