<?php
/**
 * File containing the {@see Mailcode_Commands_Command} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Validation helper for a command's parameters statement.
 * It is the counterpart to the tokenizer: The tokenizer
 * finds the tokens, and ensures the syntax is correct. The
 * validator then allows checking the correct tokens are
 * present for the command.
 * 
 * Offers a range of methods that make it easy to check 
 * whether expected parameters exist, enforcing a specific
 * order, or freeform.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Validator
{
   /**
    * @var Mailcode_Parser_Statement
    */
    private $statement;
    
    public function __construct(Mailcode_Parser_Statement $statement)
    {
        $this->statement = $statement;
    }
    
   /**
    * Creates a variable validator: checks whether a single
    * variable token is present in the parameters.
    * 
    * @return Mailcode_Parser_Statement_Validator_Type_Variable
    */
    public function createVariable() : Mailcode_Parser_Statement_Validator_Type_Variable
    {
        $validate = new Mailcode_Parser_Statement_Validator_Type_Variable($this->statement);
        
        return $validate;
    }
    
   /**
    * Creates a keyword validator: checks whether a single
    * keyword token is present in the parameters.
    * 
    * @param string $keywordName
    * @return Mailcode_Parser_Statement_Validator_Type_Keyword
    */
    public function createKeyword(string $keywordName) : Mailcode_Parser_Statement_Validator_Type_Keyword
    {
        $validate = new Mailcode_Parser_Statement_Validator_Type_Keyword(
            $this->statement,
            $keywordName
        );
        
        return $validate;
    }
    
    public function createStringLiteral() : Mailcode_Parser_Statement_Validator_Type_StringLiteral
    {
        $validate = new Mailcode_Parser_Statement_Validator_Type_StringLiteral($this->statement);
        
        return $validate;
    }
    
    public function createValue() : Mailcode_Parser_Statement_Validator_Type_Value
    {
        $validate = new Mailcode_Parser_Statement_Validator_Type_Value($this->statement);
        
        return $validate;
    }
    
    public function createOperand(string $sign='') : Mailcode_Parser_Statement_Validator_Type_Operand
    {
        $validate = new Mailcode_Parser_Statement_Validator_Type_Operand($this->statement);
        
        if(!empty($sign))
        {
            $validate->setOperandSign($sign);
        }
        
        return $validate;
    }
}
