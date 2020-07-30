<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Parser_SingleLinesFormatterTests extends MailcodeTestCase
{
    public function test_makeSafe_separateLines()
    {
        $tests = array(
            array(
                'label' => 'Within a sentence, without newlines',
                'text' => 'Text with a {setvar: $FOOBAR = "Value"} variable.',
                'expected' =>  
'Text with a 
{setvar: $FOOBAR = "Value"}
 variable.'
            ),
            array(
                'label' => 'At the start of the string, with text appended',
                'text' => '{setvar: $FOOBAR = "Value"} and some text.',
                'expected' => 
'{setvar: $FOOBAR = "Value"}
 and some text.'
            ),
            array(
                'label' => 'At the end of the string.',
                'text' => 'Some text and {setvar: $FOOBAR = "Value"}',
                'expected' => 
'Some text and 
{setvar: $FOOBAR = "Value"}'
            ),
            array(
                'label' => 'On its own line, but with text appended',
                'text' => 
'Some text here.
{setvar: $FOOBAR = "Value"} also here.',
                'expected' => 
'Some text here.
{setvar: $FOOBAR = "Value"}
 also here.'            
            ),
            array(
                'label' => 'No newlines needed',
                'text' => '{setvar: $FOOBAR = "Value"}',
                'expected' => '{setvar: $FOOBAR = "Value"}'
            ),
            array(
                'label' => 'Single char before and after (shorter than the EOL character)',
                'text' => '-{setvar: $FOOBAR = "Value"}-',
                'expected' => 
'-
{setvar: $FOOBAR = "Value"}
-'
            ),
            array(
                'label' => 'Unicode chars in the command (potential string length issues)',
                'text' => '{if variable: $FOOBAR == "öäü"}Text here{end}',
                'expected' =>
                '{if variable: $FOOBAR == "öäü"}
Text here
{end}'
            ),
            array(
                'label' => 'Show variable commands should not be modified (they generate content)',
                'text' => 'This is {showvar: $FOOBAR} a regular variable.',
                'expected' => 'This is {showvar: $FOOBAR} a regular variable.'
            ),
            array(
                'label' => 'Several commands following each other',
                'text' => '{if: 0 == 1}{if: 0 == 2}{end}{end}',
                'expected' => 
'{if: 0 == 1}
{if: 0 == 2}
{end}
{end}'
            )
        );
        
        $parser = Mailcode::create()->getParser();

        foreach($tests as $test)
        {
            $safeguard = $parser->createSafeguard($test['text']);
            $safeguard->selectSingleLinesFormatter();
            
            try
            {
                $safe = $safeguard->makeSafe();
                $result = $safeguard->makeWhole($safe);
            }
            catch(Mailcode_Exception $e)
            {
                $this->fail(sprintf(
                    'Exception: #%2$s %3$s %1$s Details: %4$s %1$s Safe string: %1$s %5$s',
                    PHP_EOL,
                    $e->getCode(),
                    $e->getMessage(),
                    $e->getDetails(),
                    $safe
                ));
            }
            
            $this->assertEquals($test['expected'], $result, $test['label']);
        }
    }
}
