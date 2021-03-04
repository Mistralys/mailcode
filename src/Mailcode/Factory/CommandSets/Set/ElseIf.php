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
    
    public function var(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, $operand, $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVariable', $command);
    }
    
    public function varString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, $operand, $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarString', $command);
    }
    
    public function varEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '==', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarEquals', $command);
    }
    
    public function varEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '==', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarEqualsString', $command);
    }
    
    public function varNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = $this->instantiator->buildIfVar('ElseIf', $variable, '!=', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfVarNotEquals', $command);
    }
    
    public function varNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
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
     * @throws Mailcode_Factory_Exception
     */
    public function contains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_Contains
    {
        $command = $this->instantiator->buildIfContains('ElseIf', $variable, $searchTerms, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Contains)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @return Mailcode_Commands_Command_ElseIf_NotContains
     * @throws Mailcode_Factory_Exception
     */
    public function notContains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_NotContains
    {
        $command = $this->instantiator->buildIfNotContains('ElseIf', $variable, $searchTerms, $caseInsensitive);

        if($command instanceof Mailcode_Commands_Command_ElseIf_NotContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfNotContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_ElseIf_ListContains
     * @throws Mailcode_Factory_Exception
     */
    public function listContains(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_ElseIf_ListContains
    {
        $command = $this->instantiator->buildIfListContains('ElseIf', $variable, $searchTerms, $caseInsensitive, $regexEnabled);

        if($command instanceof Mailcode_Commands_Command_ElseIf_ListContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfListContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_ElseIf_ListNotContains
     * @throws Mailcode_Factory_Exception
     */
    public function listNotContains(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_ElseIf_ListNotContains
    {
        $command = $this->instantiator->buildIfListNotContains('ElseIf', $variable, $searchTerms, $caseInsensitive, $regexEnabled);

        if($command instanceof Mailcode_Commands_Command_ElseIf_ListNotContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfListNotContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_ElseIf_ListBeginsWith
     * @throws Mailcode_Factory_Exception
     */
    public function listBeginsWith(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_ElseIf_ListBeginsWith
    {
        $command = $this->instantiator->buildIfListContains('ElseIf', $variable, $searchTerms, $caseInsensitive, $regexEnabled, 'list-begins-with');

        if($command instanceof Mailcode_Commands_Command_ElseIf_ListBeginsWith)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfListBeginsWith', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_ElseIf_ListEndsWith
     * @throws Mailcode_Factory_Exception
     */
    public function listEndsWith(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_ElseIf_ListEndsWith
    {
        $command = $this->instantiator->buildIfListContains('ElseIf', $variable, $searchTerms, $caseInsensitive, $regexEnabled, 'list-ends-with');

        if($command instanceof Mailcode_Commands_Command_ElseIf_ListEndsWith)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfListEndsWith', $command);
    }

    public function beginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_BeginsWith
    {
        $command = $this->instantiator->buildIfBeginsWith('ElseIf', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_BeginsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfBeginsWith', $command);
    }
    
    public function endsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_EndsWith
    {
        $command = $this->instantiator->buildIfEndsWith('ElseIf', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_EndsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfEndsWith', $command);
    }
    
    public function empty(string $variable) : Mailcode_Commands_Command_ElseIf_Empty
    {
        $command = $this->instantiator->buildIfEmpty('ElseIf', $variable);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Empty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfEmpty', $command);
    }
    
    public function notEmpty(string $variable) : Mailcode_Commands_Command_ElseIf_NotEmpty
    {
        $command = $this->instantiator->buildIfNotEmpty('ElseIf', $variable);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_NotEmpty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('ElseIfNotEmpty', $command);
    }

    public function biggerThan(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_BiggerThan
    {
        $command = $this->instantiator->buildIfBiggerThan('ElseIf', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_ElseIf_BiggerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfBiggerThan', $command);
    }

    public function smallerThan(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_SmallerThan
    {
        $command = $this->instantiator->buildIfSmallerThan('ElseIf', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_ElseIf_SmallerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfSmallerThan', $command);
    }

    public function equalsNumber(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_EqualsNumber
    {
        $command = $this->instantiator->buildIfEquals('ElseIf', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_ElseIf_EqualsNumber)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('ElseIfEqualsNumber', $command);
    }
}
