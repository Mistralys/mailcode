<?php

use Mailcode\Mailcode;

final class Mailcode_Commands_NormalizeTests extends MailcodeTestCase
{
    public function test_normalizeShowVar()
    {
        $tests = array(
            array(
                'label' => 'Show variable',
                'string' => '{showvar:   $FOO    .      BAR}',
                'expected' => '{showvar: $FOO.BAR}'
            ),
            array(
                'label' => 'Set variable with spacing',
                'string' => '{setvar:   $FOO.BAR =           "Value"   }',
                'expected' => '{setvar: $FOO.BAR = "Value"}'
            ),
            array(
                'label' => 'IF command with spacing',
                'string' => '{   if       variable  :   $FOO.BAR ==           "Value"   }{end}',
                'expected' => '{if variable: $FOO.BAR == "Value"}'
            ),
            array(
                'label' => 'FOR command with spacing',
                'string' => '{   for       :   $NAME in:           $FOO   }{end}',
                'expected' => '{for: $NAME in: $FOO}'
            ),
            array(
                'label' => 'Normalizing pretty quote style',
                'string' => '{if variable: $NAME == “value”}{end}',
                'expected' => '{if variable: $NAME == "value"}'
            )
        );
       
        $parser = Mailcode::create()->getParser();
       
        foreach($tests as $test)
        {
            $result = $parser->parseString($test['string']);
           
            $command = $result->getFirstCommand();
            
            $this->assertEquals($test['expected'], $command->getNormalized(), $test['label']);
        }
    }
    
    public function test_normalizeShowDate()
    {
        $tests = array(
            array(
                'label' => 'Show date',
                'string' => '{showdate: $FOOBAR}',
                'expected' => '{showdate: $FOOBAR}'
            ),
            array(
                'label' => 'Show date with date',
                'string' => '{showdate: $FOOBAR "Y-m-d"}',
                'expected' => '{showdate: $FOOBAR "Y-m-d"}'
            ),
            array(
                'label' => 'Show date with date and time',
                'string' => '{showdate: $FOOBAR "Y-m-d H:i:s"}',
                'expected' => '{showdate: $FOOBAR "Y-m-d H:i:s"}'
            )
        );
        
        $parser = Mailcode::create()->getParser();
        
        foreach($tests as $test)
        {
            $result = $parser->parseString($test['string']);
            
            $command = $result->getFirstCommand();
            
            $this->assertEquals($test['expected'], $command->getNormalized(), $test['label']);
        }
    }
}
