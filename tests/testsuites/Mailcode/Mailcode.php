<?php

use Mailcode\Mailcode;

final class Mailcode_MailcodeTests extends MailcodeTestCase
{
    public function test_createCode()
    {
        $code = Mailcode::create();
        
        $this->assertInstanceOf(Mailcode::class, $code);
    }
   
    public function test_findVariableNames()
    {
        $tests = array(
            array(
                'label' => 'Empty string',
                'string' => '',
                'amount' => 0,
                'expected' => array()
            ),
            array(
                'label' => 'Invalid variable because path starts with number',
                'string' => '$4OO.BAR',
                'amount' => 0,
                'expected' => array()
            ),
            array(
                'label' => 'Invalid variable because name starts with number',
                'string' => '$FOO.8AR',
                'amount' => 0,
                'expected' => array()
            ),
            array(
                'label' => 'Single variable',
                'string' => '{showvar:$VAR.NAME}',
                'amount' => 1,
                'expected' => array(
                    '$VAR.NAME' => 1
                )
            ),
            array(
                'label' => 'Single variable with whitespace around it',
                'string' => "{showvar: \n  \t \n\$VAR.NAME  \t }",
                'amount' => 1,
                'expected' => array(
                    '$VAR.NAME' => 1
                )
            ),
            array(
                'label' => 'Single variable with two different spacing styles',
                'string' => "{command:\$VAR.NAME} {command: \$VAR\n.\tNAME }",
                'amount' => 2,
                'expected' => array(
                    '$VAR.NAME' => 2
                )
            ),
            array(
                'label' => 'Mixed variables',
                'string' => '<$VAR.NAME> <$FOO.BAR> <$BAR.FOO> <$VAR.LASTNAME>',
                'amount' => 4,
                'expected' => array(
                    '$VAR.NAME' => 1,
                    '$FOO.BAR' => 1,
                    '$BAR.FOO' => 1,
                    '$VAR.LASTNAME' => 1
                )
            )
        );
        
        $mailcode = Mailcode::create();
        
        foreach($tests as $test)
        {
            $collection = $mailcode->findVariables($test['string']);
            
            $this->assertSame($test['amount'], $collection->countVariables(), $test['label']);
            
            foreach($test['expected'] as $name => $amount)
            {
                $nameCollection = $collection->getByFullName($name);
                
                $this->assertTrue($collection->hasVariableName($name));
                $this->assertSame($amount, $nameCollection->countVariables());
            }
        }
    }
    
    public function test_invalidVariables()
    {
        $mailcode = Mailcode::create();
        
        $collection = $mailcode->findVariables('$FOO.8AR');
        
        $this->assertTrue($collection->hasInvalid());
        $this->assertSame(1, $collection->getInvalid()->countVariables());
    }
}
