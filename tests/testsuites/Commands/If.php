<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_IfBase;

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
                'code' => Mailcode_Commands_IfBase::VALIDATION_OPERAND_MISSING
            ),
            array(
                'label' => 'Using assignment, not comparison',
                'string' => '{if variable: $FOO = "Some text"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_IfBase::VALIDATION_OPERAND_NOT_COMPARISON
            ),
            array(
                'label' => 'Without comparison value',
                'string' => '{if variable: $FOO == }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_IfBase::VALIDATION_NOTHING_AFTER_OPERAND
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
                'code' => Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Nothing after variable',
                'string' => '{if contains: $FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_IfBase::VALIDATION_EXPECTED_KEYWORD
            ),
            array(
                'label' => 'Keyword, but no string',
                'string' => '{if contains: $FOO insensitive:}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_IfBase::VALIDATION_CONTAINS_MISSING_SEARCH_TERM
            ),
            array(
                'label' => 'Wrong keyword',
                'string' => '{if contains: $FOO in: "Search"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_IfBase::VALIDATION_INVALID_KEYWORD
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
                'code' => Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
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
                'code' => Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
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
}
