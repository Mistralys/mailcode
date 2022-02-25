<?php

declare(strict_types=1);

namespace testsuites\Validator;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Validator;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Keyword;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Operand;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Value;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Variable;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_StringLiteral;
use MailcodeTestCase;

final class ValidationTests extends MailcodeTestCase
{
    // region: _Tests

    public function test_variable() : void
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createVariable();
        
        $this->assertTrue($val->isValid(), 'Variable should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }
    
    public function test_variable_missing() : void
    {
        $validator = $this->createValidator('"String"');
        
        $val = $validator->createVariable();
        
        $this->assertFalse($val->isValid());

        $this->expectExceptionCode(Mailcode_Parser_Statement_Validator_Type_Variable::ERROR_NO_VARIABLE_TOKEN_FOUND);

        $val->getToken();
    }
    
    public function test_string() : void
    {
        $validator = $this->createValidator('"Foobar"');
        
        $val = $validator->createStringLiteral();
        
        $this->assertTrue($val->isValid(), 'String literal should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }
    
    public function test_string_missing() : void
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createStringLiteral();
        
        $this->assertFalse($val->isValid());

        $this->expectExceptionCode(Mailcode_Parser_Statement_Validator_Type_StringLiteral::ERROR_NO_STRING_TOKEN_FOUND);

        $val->getToken();
    }
    
    public function test_keyword() : void
    {
        $validator = $this->createValidator(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $val = $validator->createKeyword(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $this->assertTrue($val->isValid(), 'Keyword should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }

    public function test_keyword_hyphen() : void
    {
        $validator = $this->createValidator(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $val = $validator->createKeyword(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $this->assertTrue($val->isValid(), 'Keyword should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }
    
    public function test_keyword_missing() : void
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createKeyword(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $this->assertFalse($val->isValid());

        $this->expectExceptionCode(Mailcode_Parser_Statement_Validator_Type_Keyword::ERROR_NO_KEYWORD_TOKEN_FOUND);

        $val->getToken();
    }
    
    public function test_multi() : void
    {
        $statement = self::$mailcode->getParser()->createStatement(' $VAR "Some text" insensitive: ');
        
        $validator = new Mailcode_Parser_Statement_Validator($statement);
        
        $var = $validator->createVariable();
        $str = $validator->createStringLiteral();
        $key = $validator->createKeyword(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $this->assertTrue($var->isValid(), 'Variable should be present');
        $this->assertTrue($str->isValid(), 'String literal should be present');
        $this->assertTrue($key->isValid(), 'Keyword should be present');
    }
    
    public function test_index() : void
    {
        $statement = self::$mailcode->getParser()->createStatement(' $VAR "Some text" insensitive: ');
        
        $validator = new Mailcode_Parser_Statement_Validator($statement);
        
        $var = $validator->createVariable()->setIndex(1);
        
        $this->assertFalse($var->isValid());
        
        $var2 = $validator->createVariable()->setIndex(0);
        
        $this->assertTrue($var2->isValid());
    }
    
    public function test_value_string() : void
    {
        $validator = $this->createValidator('"Value"');
        
        $val = $validator->createValue();
        
        $this->assertTrue($val->isValid(), 'Value should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }

    public function test_value_number() : void
    {
        $validator = $this->createValidator('45');
        
        $val = $validator->createValue();
        
        $this->assertTrue($val->isValid(), 'Value should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }
    
    public function test_value_missing() : void
    {
        $validator = $this->createValidator(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $val = $validator->createValue();
        
        $this->assertFalse($val->isValid());

        $this->expectExceptionCode(Mailcode_Parser_Statement_Validator_Type_Value::ERROR_NO_VALUE_TOKEN_FOUND);

        $val->getToken();
    }
    
    public function test_operand() : void
    {
        $validator = $this->createValidator('==');
        
        $val = $validator->createOperand();
        
        $this->assertTrue($val->isValid(), 'Operand should be present');

        // No exception = token is present
        $val->getToken();
        $this->addToAssertionCount(1);
    }
    
    public function test_operand_sign() : void
    {
        $validator = $this->createValidator('==');
        
        $val = $validator->createOperand('==');
        
        $this->assertTrue($val->isValid(), 'Operand should be present');
        
        $val = $validator->createOperand('=');
        
        $this->assertFalse($val->isValid(), 'Operand should not be present');
    }

    public function test_operand_missing() : void
    {
        $validator = $this->createValidator(Mailcode_Commands_Keywords::TYPE_INSENSITIVE);
        
        $val = $validator->createOperand();
        
        $this->assertFalse($val->isValid());

        $this->expectExceptionCode(Mailcode_Parser_Statement_Validator_Type_Operand::ERROR_NO_OPERAND_TOKEN_FOUND);

        $val->getToken();
    }

    // endregion

    // region: Support methods

    /**
     * @var Mailcode
     */
    static private Mailcode $mailcode;

    protected function setUp() : void
    {
        parent::setUp();

        if(!isset(self::$mailcode))
        {
            self::$mailcode = Mailcode::create();
        }
    }

    private function createValidator(string $statementString) : Mailcode_Parser_Statement_Validator
    {
        $statement = self::$mailcode->getParser()->createStatement($statementString);

        return new Mailcode_Parser_Statement_Validator($statement);
    }

    // endregion
}
