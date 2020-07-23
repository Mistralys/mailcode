<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_ElseIf} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory_CommandSets_Set_ElseIf
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_ElseIf extends Mailcode_Factory_CommandSets_IfBase
{
    public function elseIf(string $condition, string $type='') : Mailcode_Commands_Command_ElseIf
    {
        $command = $this->instantiator->buildIf('ElseIf', $condition, $type);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIf', $command);
    }
    
    public function elseIfVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, $operand, $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVariable', $command);
    }
    
    public function elseIfVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, $operand, $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarString', $command);
    }
    
    public function elseIfVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '==', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarEquals', $command);
    }
    
    public function elseIfVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '==', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarEqualsString', $command);
    }
    
    public function elseIfVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '!=', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarNotEquals', $command);
    }
    
    public function elseIfVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '!=', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarNotEqualsString', $command);
    }
    
   /**
    * @param string $variable
    * @param string[] $searchTerms
    * @param bool $caseInsensitive
    * @return Mailcode_Commands_Command_ElseIf_Contains
    */
    public function elseIfContains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_Contains
    {
        $command = $this->instantiator->buildIfContains('ElseIf', $variable, $searchTerms, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Contains)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfContains', $command);
    }
    
    public function elseIfBeginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_BeginsWith
    {
        $command = $this->instantiator->buildIfBeginsWith('ElseIf', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_BeginsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfBeginsWith', $command);
    }
    
    public function elseIfEndsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_EndsWith
    {
        $command = $this->instantiator->buildIfEndsWith('ElseIf', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_EndsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfEndsWith', $command);
    }
    
    public function elseIfEmpty(string $variable) : Mailcode_Commands_Command_ElseIf_Empty
    {
        $command = $this->instantiator->buildIfEmpty('ElseIf', $variable);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Empty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfEmpty', $command);
    }
    
    public function elseIfNotEmpty(string $variable) : Mailcode_Commands_Command_ElseIf_NotEmpty
    {
        $command = $this->instantiator->buildIfNotEmpty('ElseIf', $variable);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_NotEmpty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfNotEmpty', $command);
    }
}
