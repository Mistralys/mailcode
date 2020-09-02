<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Parser_SingleLinesFormatterTests extends MailcodeTestCase
{
   /**
    * NOTE: To be newline-style-agnostic, the test uses the placeholder
    * [EOL] to mark where a newline should be inserted. The same 
    * string is used as the starting text, but with the placeholders
    * stripped out.
    */
    public function test_makeSafe_separateLines()
    {
        $tests = array(
            array(
                'label' => 'Within a sentence, without newlines',
                'text' => 'Text with a [EOL]{setvar: $FOOBAR = "Value"}[EOL] variable.',
            ),
            array(
                'label' => 'At the start of the string, with text appended',
                'text' => '{setvar: $FOOBAR = "Value"}[EOL] and some text.',
            ),
            array(
                'label' => 'At the end of the string.',
                'text' => 'Some text and [EOL]{setvar: $FOOBAR = "Value"}',
            ),
            array(
                'label' => 'On its own line, but with text appended',
                'text' => 
'Some text here.
{setvar: $FOOBAR = "Value"}[EOL] also here.',
            ),
            array(
                'label' => 'No newlines needed',
                'text' => '{setvar: $FOOBAR = "Value"}',
                'expected' => '{setvar: $FOOBAR = "Value"}'
            ),
            array(
                'label' => 'Single char before and after (shorter than the EOL character)',
                'text' => '-[EOL]{setvar: $FOOBAR = "Value"}[EOL]-',
            ),
            array(
                'label' => 'Unicode chars in the command (potential string length issues)',
                'text' => '{if variable: $FOOBAR == "öäü"}[EOL]Text here[EOL]{end}',
            ),
            array(
                'label' => 'Show variable commands should not be modified (they generate content)',
                'text' => 'This is {showvar: $FOOBAR} a regular variable.',
                'expected' => 'This is {showvar: $FOOBAR} a regular variable.'
            ),
            array(
                'label' => 'Several commands following each other',
                'text' => '{if: 0 == 1}[EOL]{if: 0 == 2}[EOL]{end}[EOL]{end}',
            )
        );
        
        $parser = Mailcode::create()->getParser();

        foreach($tests as $test)
        {
            $noEOL = str_replace('[EOL]', '', $test['text']);
            
            $safeguard = $parser->createSafeguard($noEOL);
            $label = $test['label']; 
            
            try
            {
                $safe = $safeguard->makeSafe();
                
                $formatting = $safeguard->createFormatting($safeguard->makeSafe());
                $formatter = $formatting->formatWithSingleLines();
                
                $withEOL = str_replace('[EOL]', $formatter->getEOLChar(), $test['text']);
                
                $result = $formatting->toString();

                // test fail details
                $label .= PHP_EOL.'Formatter log:'.PHP_EOL.
                implode(PHP_EOL, $formatter->getLog());
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
            
            $this->assertEquals($withEOL, $result, $label);
        }
    }
}
