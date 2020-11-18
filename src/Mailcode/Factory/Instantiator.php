<?php
/**
 * File containing the {@see Mailcode_Factory_Instantiator} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory_Instantiator
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
class Mailcode_Factory_Instantiator
{
    public function buildIf(string $ifType, string $params, string $type='') : Mailcode_Commands_IfBase
    {
        $stringType = $type;
        
        if(!empty($type))
        {
            $stringType = ' '.$type;
        }
        
        $command = Mailcode::create()->getCommands()->createCommand(
            $ifType,
            $type,
            $params,
            sprintf(
                '{%s%s: %s}',
                strtolower($ifType),
                $stringType,
                $params
            )
        );
        
        $this->checkCommand($command);
        
        if($command instanceof Mailcode_Commands_IfBase)
        {
            return $command;
        }
        
        throw $this->exceptionUnexpectedType('IfBase', $command);
    }
    
    public function buildIfVar(string $ifType, string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_IfBase
    {
        if($quoteValue)
        {
            $value = $this->quoteString($value);
        }
        
        $condition = sprintf(
            "%s %s %s",
            $this->filterVariableName($variable),
            $operand,
            $value
        );
        
        return $this->buildIf($ifType, $condition, 'variable');
    }
    
    public function buildIfEmpty(string $ifType, string $variable) : Mailcode_Commands_IfBase
    {
        return $this->buildIf($ifType, $this->filterVariableName($variable), 'empty');
    }
    
    public function buildIfNotEmpty(string $ifType, string $variable) : Mailcode_Commands_IfBase
    {
        return $this->buildIf($ifType, $this->filterVariableName($variable), 'not-empty');
    }
    
   /**
    * @param string $ifType
    * @param string $variable
    * @param string[] $searchTerms
    * @param bool $caseInsensitive
    * @return Mailcode_Commands_IfBase
    */
    public function buildIfContains(string $ifType, string $variable, array $searchTerms, bool $caseInsensitive=false) : Mailcode_Commands_IfBase
    {
        $keyword = ' ';
        
        if($caseInsensitive)
        {
            $keyword = ' '.Mailcode_Commands_Keywords::TYPE_INSENSITIVE;
        }
        
        $condition = sprintf(
            '%s%s"%s"',
            $this->filterVariableName($variable),
            $keyword,
            implode('" "', array_map(array($this, 'filterLiteral'), $searchTerms))
        );
        
        return $this->buildIf($ifType, $condition, 'contains');
    }
    
    public function buildIfBeginsWith(string $ifType, string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_IfBase
    {
        return $this->buildIfSearch($ifType, 'begins-with', $variable, $search, $caseInsensitive);
    }
    
    public function buildIfEndsWith(string $ifType, string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_IfBase
    {
        return $this->buildIfSearch($ifType, 'ends-with', $variable, $search, $caseInsensitive);
    }

    private function buildIfNumeric(string $ifType, string $variable, string $value, string $type) : Mailcode_Commands_IfBase
    {
        $params = sprintf(
            '%1$s "%2$s"',
            '$'.ltrim($variable, '$'),
            $value
        );

        return $this->buildIf(
            $ifType,
            $params,
            $type
        );
    }

    public function buildIfBiggerThan(string $ifType, string $variable, string $value) : Mailcode_Commands_IfBase
    {
        return $this->buildIfNumeric(
            $ifType,
            $variable,
            $value,
            'bigger-than'
        );
    }

    public function buildIfSmallerThan(string $ifType, string $variable, string $value) : Mailcode_Commands_IfBase
    {
        return $this->buildIfNumeric(
            $ifType,
            $variable,
            $value,
            'smaller-than'
        );
    }

    public function buildIfEquals(string $ifType, string $variable, string $value) : Mailcode_Commands_IfBase
    {
        return $this->buildIfNumeric(
            $ifType,
            $variable,
            $value,
            'equals-number'
        );
    }

    private function buildIfSearch(string $ifType, string $subType, string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_IfBase
    {
        $keyword = ' ';
        
        if($caseInsensitive)
        {
            $keyword = ' '.Mailcode_Commands_Keywords::TYPE_INSENSITIVE;
        }
        
        $condition = sprintf(
            '%s%s"%s"',
            $this->filterVariableName($variable),
            $keyword,
            $this->filterLiteral($search)
        );
        
        return $this->buildIf($ifType, $condition, $subType);
    }
    
    public function filterLiteral(string $term) : string
    {
        return str_replace('"', '\"', $term);
    }
    
    public function filterVariableName(string $name) : string
    {
        $name = preg_replace('/\s/', '', $name);
        
        return '$'.ltrim($name, '$');
    }
    
    public function checkCommand(Mailcode_Commands_Command $command) : void
    {
        if($command->isValid())
        {
            return;
        }
        
        throw new Mailcode_Factory_Exception(
            'Invalid command created.',
            'Validation message: '.$command->getValidationResult()->getErrorMessage(),
            Mailcode_Factory::ERROR_INVALID_COMMAND_CREATED,
            null,
            $command
        );
    }
    
    /**
     * Quotes a string literal: adds the quotes, and escapes any quotes already present in it.
     *
     * @param string $string
     * @return string
     */
    public function quoteString(string $string) : string
    {
        return '"'.str_replace('"', '\"', $string).'"';
    }
    
    public function exceptionUnexpectedType(string $type, Mailcode_Commands_Command $command) : Mailcode_Factory_Exception
    {
        return new Mailcode_Factory_Exception(
            'Invalid command class type created.',
            sprintf('Excepted type [%s], but created class [%s].', $type, get_class($command)),
            Mailcode_Factory::ERROR_UNEXPECTED_COMMAND_TYPE,
            null,
            $command
        );
    }
}
