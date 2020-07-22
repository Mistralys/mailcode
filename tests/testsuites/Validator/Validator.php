<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_ValueInterface;
use Mailcode\Mailcode_Parser_Statement_Validator;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Keyword;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Operand;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Value;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_Variable;
use Mailcode\Mailcode_Parser_Statement_Validator_Type_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Operand;

final class Validator_ValidatorTests extends MailcodeTestCase
{
   /**
    * @var Mailcode
    */
    static private $mailcode;
    
    protected function setUp() : void
    {
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

    public function test_variable()
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createVariable();
        
        $this->assertTrue($val->isValid(), 'Variable should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Variable::class, $val->getToken(), 'Token should be present');
    }
    
    public function test_variable_missing()
    {
        $validator = $this->createValidator('"String"');
        
        $val = $validator->createVariable();
        
        $this->assertFalse($val->isValid());
        
        try
        {
            $val->getToken();
        }
        catch (Mailcode_Exception $e)
        {
            if($e->getCode() === Mailcode_Parser_Statement_Validator_Type_Variable::ERROR_NO_VARIABLE_TOKEN_FOUND)
            {
                $this->addToAssertionCount(1);
                return;
            }
        }
        
        $this->fail('No exception triggered');
    }
    
    public function test_string()
    {
        $validator = $this->createValidator('"Foobar"');
        
        $val = $validator->createStringLiteral();
        
        $this->assertTrue($val->isValid(), 'String literal should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $val->getToken(), 'Token should be present');
    }
    
    public function test_string_missing()
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createStringLiteral();
        
        $this->assertFalse($val->isValid());
        
        try
        {
            $val->getToken();
        }
        catch (Mailcode_Exception $e)
        {
            if($e->getCode() === Mailcode_Parser_Statement_Validator_Type_StringLiteral::ERROR_NO_STRING_TOKEN_FOUND)
            {
                $this->addToAssertionCount(1);
                return;
            }
        }
        
        $this->fail('No exception triggered');
    }
    
    public function test_keyword()
    {
        $validator = $this->createValidator('insensitive:');
        
        $val = $validator->createKeyword('insensitive');
        
        $this->assertTrue($val->isValid(), 'Keyword should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Keyword::class, $val->getToken(), 'Token should be present');
    }

    public function test_keyword_hyphen()
    {
        $validator = $this->createValidator('insensitive:');
        
        $val = $validator->createKeyword('insensitive:');
        
        $this->assertTrue($val->isValid(), 'Keyword should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Keyword::class, $val->getToken(), 'Token should be present');
    }
    
    public function test_keyword_missing()
    {
        $validator = $this->createValidator('$VAR');
        
        $val = $validator->createKeyword('insensitive');
        
        $this->assertFalse($val->isValid());
        
        try
        {
            $val->getToken();
        }
        catch (Mailcode_Exception $e)
        {
            if($e->getCode() === Mailcode_Parser_Statement_Validator_Type_Keyword::ERROR_NO_KEYWORD_TOKEN_FOUND)
            {
                $this->addToAssertionCount(1);
                return;
            }
        }
        
        $this->fail('No exception triggered');
    }
    
    public function test_multi()
    {
        $statement = self::$mailcode->getParser()->createStatement(' $VAR "Some text" insensitive: ');
        
        $validator = new Mailcode_Parser_Statement_Validator($statement);
        
        $var = $validator->createVariable();
        $str = $validator->createStringLiteral();
        $key = $validator->createKeyword('insensitive');
        
        $this->assertTrue($var->isValid(), 'Variable should be present');
        $this->assertTrue($str->isValid(), 'String literal should be present');
        $this->assertTrue($key->isValid(), 'Keyword should be present');
    }
    
    public function test_index()
    {
        $statement = self::$mailcode->getParser()->createStatement(' $VAR "Some text" insensitive: ');
        
        $validator = new Mailcode_Parser_Statement_Validator($statement);
        
        $var = $validator->createVariable()->setIndex(1);
        
        $this->assertFalse($var->isValid());
        
        $var2 = $validator->createVariable()->setIndex(0);
        
        $this->assertTrue($var2->isValid());
    }
    
    public function test_value_string()
    {
        $validator = $this->createValidator('"Value"');
        
        $val = $validator->createValue();
        
        $this->assertTrue($val->isValid(), 'Value should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_ValueInterface::class, $val->getToken(), 'Token should be present');
    }

    public function test_value_number()
    {
        $validator = $this->createValidator('45');
        
        $val = $validator->createValue();
        
        $this->assertTrue($val->isValid(), 'Value should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_ValueInterface::class, $val->getToken(), 'Token should be present');
    }
    
    public function test_value_missing()
    {
        $validator = $this->createValidator('insensitive:');
        
        $val = $validator->createValue();
        
        $this->assertFalse($val->isValid());
        
        try
        {
            $val->getToken();
        }
        catch (Mailcode_Exception $e)
        {
            if($e->getCode() === Mailcode_Parser_Statement_Validator_Type_Value::ERROR_NO_VALUE_TOKEN_FOUND)
            {
                $this->addToAssertionCount(1);
                return;
            }
        }
        
        $this->fail('No exception triggered');
    }
    
    public function test_operand()
    {
        $validator = $this->createValidator('==');
        
        $val = $validator->createOperand();
        
        $this->assertTrue($val->isValid(), 'Operand should be present');
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Operand::class, $val->getToken(), 'Token should be present');
    }
    
    public function test_operand_sign()
    {
        $validator = $this->createValidator('==');
        
        $val = $validator->createOperand('==');
        
        $this->assertTrue($val->isValid(), 'Operand should be present');
        
        $val = $validator->createOperand('=');
        
        $this->assertFalse($val->isValid(), 'Operand should not be present');
    }
    
    
    public function test_operand_missing()
    {
        $validator = $this->createValidator('insensitive:');
        
        $val = $validator->createOperand();
        
        $this->assertFalse($val->isValid());
        
        try
        {
            $val->getToken();
        }
        catch (Mailcode_Exception $e)
        {
            if($e->getCode() === Mailcode_Parser_Statement_Validator_Type_Operand::ERROR_NO_OPERAND_TOKEN_FOUND)
            {
                $this->addToAssertionCount(1);
                return;
            }
        }
        
        $this->fail('No exception triggered');
    }
}
