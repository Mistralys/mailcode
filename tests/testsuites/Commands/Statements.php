<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Parser_Statement;

final class Mailcode_Commands_StatementsTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Empty string',
                'string' => '',
                'valid' => false,
                'code' => Mailcode_Parser_Statement::VALIDATION_EMPTY
            ),
            array(
                'label' => 'With double equals sign',
                'string' => '$FOO.BAR == "Text"',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'String literal with escaped quotes',
                'string' => '$FOO.BAR == "Text \"Haha\""',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Text without quotes',
                'string' => '$FOO.BAR == Text',
                'valid' => false,
                'code' => Mailcode_Parser_Statement::VALIDATION_UNQUOTED_STRING_LITERALS
            ),
            array(
                'label' => 'Numeric assignment',
                'string' => '$FOO.BAR = 6.48',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Brackets',
                'string' => '$FOO.BAR = "[BLABLA]"',
                'valid' => true,
                'code' => 0
            )
        );
        
        foreach($tests as $test)
        {
            $statement = Mailcode::create()->getParser()->createStatement($test['string']);
            
            $this->assertSame($test['valid'], $statement->isValid());
            
            if(!$test['valid'])
            {
                $error = $statement->getValidationResult();
                $this->assertSame($test['code'], $error->getCode());
            }
        }
    }
}
