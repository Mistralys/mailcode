<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Factory
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
    
    public function var(string $variable, string $operand, string $value, bool $quoteValue=false, bool $insensitive=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, $operand, $value, $quoteValue, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVar', $command);
    }
    
    public function varString(string $variable, string $operand, string $value, bool $insensitive=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, $operand, $value, true, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarString', $command);
    }
    
    public function varEquals(string $variable, string $value, bool $quoteValue=false, bool $insensitive=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '==', $value, $quoteValue, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarEquals', $command);
    }
    
    public function varEqualsString(string $variable, string $value, bool $insensitive=false) : Mailcode_Commands_Command_If
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '==', $value, true, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarEqualsString', $command);
    }
    
    public function varNotEquals(string $variable, string $value, bool $quoteValue=false, bool $insensitive=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '!=', $value, $quoteValue, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarNotEquals', $command);
    }
    
    public function varNotEqualsString(string $variable, string $value, bool $insensitive=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = $this->instantiator->buildIfVar('If', $variable, '!=', $value, true, $insensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfVarNotEqualsString', $command);
    }
    
    public function empty(string $variable) : Mailcode_Commands_Command_If_Empty
    {
        $command = $this->instantiator->buildIfEmpty('If', $variable);
        
        if($command instanceof Mailcode_Commands_Command_If_Empty)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfEmpty', $command);
    }
    
    public function notEmpty(string $variable) : Mailcode_Commands_Command_If_NotEmpty
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
    public function contains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
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
    public function notContains(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_NotContains
    {
        $command = $this->instantiator->buildIfNotContains('If', $variable, $searchTerms, $caseInsensitive);

        if($command instanceof Mailcode_Commands_Command_If_NotContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfNotContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_If_ListContains
     * @throws Mailcode_Factory_Exception
     */
    public function listContains(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_If_ListContains
    {
        $command = $this->instantiator->buildIfListContains('If', $variable, $searchTerms, $caseInsensitive, $regexEnabled, 'list-contains');

        if($command instanceof Mailcode_Commands_Command_If_ListContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfListContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @return Mailcode_Commands_Command_If_ListEquals
     * @throws Mailcode_Factory_Exception
     */
    public function listEquals(string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_ListEquals
    {
        $command = $this->instantiator->buildIfListContains('If', $variable, $searchTerms, $caseInsensitive, false, 'list-equals');

        if($command instanceof Mailcode_Commands_Command_If_ListEquals)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfListEquals', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_If_ListNotContains
     * @throws Mailcode_Factory_Exception
     */
    public function listNotContains(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_If_ListNotContains
    {
        $command = $this->instantiator->buildIfListNotContains('If', $variable, $searchTerms, $caseInsensitive, $regexEnabled);

        if($command instanceof Mailcode_Commands_Command_If_ListNotContains)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfNotContains', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_If_ListBeginsWith
     * @throws Mailcode_Factory_Exception
     */
    public function listBeginsWith(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_If_ListBeginsWith
    {
        $command = $this->instantiator->buildIfListContains('If', $variable, $searchTerms, $caseInsensitive, $regexEnabled,'list-begins-with');

        if($command instanceof Mailcode_Commands_Command_If_ListBeginsWith)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfListBeginsWith', $command);
    }

    /**
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_Command_If_ListEndsWith
     * @throws Mailcode_Factory_Exception
     */
    public function listEndsWith(string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_Command_If_ListEndsWith
    {
        $command = $this->instantiator->buildIfListContains('If', $variable, $searchTerms, $caseInsensitive, $regexEnabled,'list-ends-with');

        if($command instanceof Mailcode_Commands_Command_If_ListEndsWith)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfListEndsWith', $command);
    }

    public function beginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_BeginsWith
    {
        $command = $this->instantiator->buildIfBeginsWith('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_BeginsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfBeginsWith', $command);
    }
    
    public function endsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_EndsWith
    {
        $command = $this->instantiator->buildIfEndsWith('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_EndsWith)
        {
            return $command;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('IfEndsWith', $command);
    }

    public function biggerThan(string $variable, string $value) : Mailcode_Commands_Command_If_BiggerThan
    {
        $command = $this->instantiator->buildIfBiggerThan('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_BiggerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfBiggerThan', $command);
    }

    public function smallerThan(string $variable, string $value) : Mailcode_Commands_Command_If_SmallerThan
    {
        $command = $this->instantiator->buildIfSmallerThan('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_SmallerThan)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfSmallerThan', $command);
    }

    public function varEqualsNumber(string $variable, string $value) : Mailcode_Commands_Command_If_EqualsNumber
    {
        $command = $this->instantiator->buildIfEquals('If', $variable, $value);

        if($command instanceof Mailcode_Commands_Command_If_EqualsNumber)
        {
            return $command;
        }

        throw $this->instantiator->exceptionUnexpectedType('IfEqualsNumber', $command);
    }
}
