<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory
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
class Mailcode_Factory_CommandSets_Set_If extends Mailcode_Factory_CommandSets_IfBase
{
    public function if(string $condition, string $type='') : Mailcode_Commands_Command_If
    {
        $command = $this->instantiator->buildIf('If', $condition, $type);
        
        if($command instanceof Mailcode_Commands_Command_If)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('If', $command);
    }
    
    public function ifVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, $operand, $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVar', $command);
    }
    
    public function ifVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, $operand, $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarString', $command);
    }
    
    public function ifVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '==', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarEquals', $command);
    }
    
    public function ifVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '==', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfarEqualsString', $command);
    }
    
    public function ifVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '!=', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarNotEquals', $command);
    }
    
    public function ifVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '!=', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarNotEqualsString', $command);
    }
    
    public function ifEmpty(string $variable) : Mailcode_Commands_Command_If_Empty
    {
        $command = $this->instantiator->buildIfEmpty('If', $variable);
        
        if($command instanceof Mailcode_Commands_Command_If_Empty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfEmpty', $command);
    }
    
    public function ifNotEmpty(string $variable) : Mailcode_Commands_Command_If_NotEmpty
    {
        $command = $this->instantiator->buildIfNotEmpty('If', $variable);
        
        if($command instanceof Mailcode_Commands_Command_If_NotEmpty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfNotEmpty', $command);
    }
    
    public function ifContains(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
    {
        $command = $this->instantiator->buildIfContains('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Contains)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfContains', $command);
    }
}