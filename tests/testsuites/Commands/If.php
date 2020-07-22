<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Factory;

final class Mailcode_IfTests extends MailcodeTestCase
{
    public function test_validation_variable()
    {
        $tests = array(
            array(
                'label' => 'With invalid variable',
                'string' => '{if variable: foobar}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'No operand after variable',
                'string' => '{if variable: $FOO "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_OPERAND_MISSING
            ),
            array(
                'label' => 'Using assignment, not comparison',
                'string' => '{if variable: $FOO = "Some text"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
            ),
            array(
                'label' => 'Without comparison value',
                'string' => '{if variable: $FOO == }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if variable: $FOO == "Something"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $message = '';
            if(!$collection->isValid())
            {
                $message = $collection->getFirstError()->getMessage();
            }
            
            $this->assertSame($test['valid'], $collection->isValid(), $test['label'].' '.$message);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $test['label']);
            }
        }
    }
    
    public function test_validation_contains()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if contains: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Nothing after variable',
                'string' => '{if contains: $FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Keyword, but no string',
                'string' => '{if contains: $FOO insensitive:}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Wrong keyword (ignored)',
                'string' => '{if contains: $FOO in: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case sensitive',
                'string' => '{if contains: $FOO "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case insensitive',
                'string' => '{if contains: $FOO insensitive: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid despite different order',
                'string' => '{if contains:"Search" insensitive: $FOO}{end}',
                'valid' => true,
                'code' => 0
            )
            
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $message = '';
            if(!$collection->isValid())
            {
                $message = $collection->getFirstError()->getMessage();
            }
            
            $this->assertSame($test['valid'], $collection->isValid(), $test['label'].' '.$message);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $test['label']);
            }
        }
    }
    
    public function test_validation_empty()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if empty: $FOOBAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'No variable specified',
                'string' => '{if not-empty: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{if not-empty: $FOOBAR}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $label = $test['label'].PHP_EOL;
            
            if(!$collection->isValid())
            {
                $label .= "Messages:".PHP_EOL;
                
                foreach($collection->getErrors() as $error)
                {
                    $label .= $error->getMessage().PHP_EOL;
                }
            }
            
            $label .= 'Command:'.$test['string'];
            
            $this->assertSame($test['valid'], $collection->isValid(), $label);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $label);
            }
        }
    }
    
   /**
    * Check for the issue where a zero in a command 
    * would be stripped out when normalized.
    */
    public function test_normalize_zero()
    {
        $cmd = Mailcode_Factory::if("0 == 1");
        
        $this->assertEquals('{if: 0 == 1}', $cmd->getNormalized());
    }
}
