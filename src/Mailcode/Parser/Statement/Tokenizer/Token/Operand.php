<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_Operand} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_Operand
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing an operand sign.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_Operand extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public function getOperand() : string
    {
        return $this->matchedText;
    }
    
    public function getNormalized() : string
    {
        return $this->getOperand();
    }
    
   /**
    * Whether the operator is comparison related (equals, not equals, smaller, greater...).
    *  
    * @return bool
    */
    public function isComparator() : bool
    {
        return 
        $this->isEquals() || 
        $this->isNotEquals() ||
        $this->isGreaterThan() || 
        $this->isGreaterOrEquals() ||
        $this->isSmallerThan() ||
        $this->isSmallerOrEquals();
    }
    
   /**
    * Whether the operator is calculation related (minus, plus, divide, multiply).
    * 
    * @return bool
    */
    public function isCalculator() : bool
    {
        return 
        $this->isPlus() || 
        $this->isDivide() || 
        $this->isMinus() || 
        $this->isMultiply();
    }
    
    public function isAssignment() : bool
    {
        return $this->getOperand() === '=';
    }
    
    public function isEquals() : bool
    {
        return $this->getOperand() === '==';
    }
    
    public function isGreaterThan() : bool
    {
        return $this->getOperand() === '>';
    }
    
    public function isSmallerThan() : bool
    {
        return $this->getOperand() === '<';
    }
    
    public function isGreaterOrEquals() : bool
    {
        return $this->getOperand() === '>=';
    }
    
    public function isSmallerOrEquals() : bool
    {
        return $this->getOperand() === '<=';
    }
    
    public function isNotEquals() : bool
    {
        return $this->getOperand() === '!=';
    }
    
    public function isMinus() : bool
    {
        return $this->getOperand() === '-';
    }
    
    public function isMultiply() : bool
    {
        return $this->getOperand() === '*';
    }
    
    public function isDivide() : bool
    {
        return $this->getOperand() === '/';
    }
    
    public function isPlus() : bool
    {
        return $this->getOperand() === '+';
    }
}
