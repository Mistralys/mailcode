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

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @return Mailcode_Commands_Command_If_Contains
     * @throws Mailcode_Factory_Exception
     */
    public function ifContains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
    {
        $command = $this->instantiator->buildIfContains('If', $variable, $searchTerms, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Contains)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @return Mailcode_Commands_Command_If_NotContains
     * @throws Mailcode_Factory_Exception
     */
    public function ifNotContains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
    {
        $command = $this->instantiator->buildIfNotContains('If', $variable, $searchTerms, $caseInsensitive);

        if($command instanceof Mailcode_Commands_Command_If_NotContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfNotContains', $command);
    }

    public function ifBeginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_BeginsWith
    {
        $command = $this->instantiator->buildIfBeginsWith('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_BeginsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfBeginsWith', $command);
    }
    
    public function ifEndsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_EndsWith
    {
        $command = $this->instantiator->buildIfEndsWith('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_EndsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfEndsWith', $command);
    }

    public function ifBiggerThan(string $variable, string $value) : Mailcode_Commands_Command_If_BiggerThan
    {
        $command = $this->instantiator->buildIfBiggerThan('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_BiggerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfBiggerThan', $command);
    }

    public function ifSmallerThan(string $variable, string $value) : Mailcode_Commands_Command_If_SmallerThan
    {
        $command = $this->instantiator->buildIfSmallerThan('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_SmallerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfSmallerThan', $command);
    }

    public function ifVarEqualsNumber(string $variable, string $value) : Mailcode_Commands_Command_If_EqualsNumber
    {
        $command = $this->instantiator->buildIfEquals('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_EqualsNumber)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfEqualsNumber', $command);
    }
}
