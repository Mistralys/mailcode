<?php
/**
 * File containing the {@see Mailcode_Factory_Instantiator} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see Mailcode_Factory_Instantiator
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
    
    public function buildIfVar(string $ifType, string $variable, string $operand, string $value, bool $quoteValue=false, bool $insensitive=false) : Mailcode_Commands_IfBase
    {
        $variable = $this->filterVariableName($variable);

        if($insensitive)
        {
            $value = mb_strtolower($value);
        }

        if($quoteValue)
        {
            $value = $this->quoteString($value);
        }
        
        $condition = sprintf(
            "%s %s %s",
            $variable,
            $operand,
            $value
        );

        if($insensitive)
        {
            $condition .= ' '.Mailcode_Commands_Keywords::TYPE_INSENSITIVE;
        }

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
     * @param bool $regexEnabled
     * @param string $containsType
     * @return Mailcode_Commands_IfBase
     * @throws Mailcode_Factory_Exception
     */
    public function buildIfContains(string $ifType, string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false, string $containsType='contains') : Mailcode_Commands_IfBase
    {
        $condition = sprintf(
            '%s%s"%s"',
            $this->filterVariableName($variable),
            $this->renderListKeywords($caseInsensitive, $regexEnabled),
            implode('" "', array_map(array($this, 'filterLiteral'), $searchTerms))
        );
        
        return $this->buildIf($ifType, $condition, $containsType);
    }

    private function renderListKeywords(bool $caseInsensitive=false, bool $regexEnabled=false) : string
    {
        $keywords = array();

        if($caseInsensitive)
        {
            $keywords[] = Mailcode_Commands_Keywords::TYPE_INSENSITIVE;
        }

        if($regexEnabled)
        {
            $keywords[] = Mailcode_Commands_Keywords::TYPE_REGEX;
        }

        $keywordsString = '';

        if(!empty($keywords))
        {
            $keywordsString = ' '.implode(' ', $keywords);
        }

        return $keywordsString;
    }

    /**
     * @param string $ifType
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_IfBase
     * @throws Mailcode_Factory_Exception
     */
    public function buildIfNotContains(string $ifType, string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_IfBase
    {
        return $this->buildIfContains($ifType, $variable, $searchTerms, $caseInsensitive, $regexEnabled, 'not-contains');
    }

    /**
     * @param string $ifType
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @param string $containsType
     * @return Mailcode_Commands_IfBase
     * @throws Mailcode_Factory_Exception
     */
    public function buildIfListContains(string $ifType, string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false, string $containsType='list-contains') : Mailcode_Commands_IfBase
    {
        return $this->buildIfContains($ifType, $variable, $searchTerms, $caseInsensitive, $regexEnabled, $containsType);
    }

    /**
     * @param string $ifType
     * @param string $variable
     * @param string[] $searchTerms
     * @param bool $caseInsensitive
     * @param bool $regexEnabled
     * @return Mailcode_Commands_IfBase
     * @throws Mailcode_Factory_Exception
     */
    public function buildIfListNotContains(string $ifType, string $variable, array $searchTerms, bool $caseInsensitive=false, bool $regexEnabled=false) : Mailcode_Commands_IfBase
    {
        return $this->buildIfContains($ifType, $variable, $searchTerms, $caseInsensitive, $regexEnabled, 'list-not-contains');
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
        $name = (string)preg_replace('/\s/', '', $name);
        
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
     * Configures the command's URL encoding or decoding, depending
     * on the selected mode.
     *
     * @param Mailcode_Commands_Command $cmd
     * @param string $urlEncoding
     * @throws Mailcode_Exception
     *
     * @see Mailcode_Factory::URL_ENCODING_NONE
     * @see Mailcode_Factory::URL_ENCODING_ENCODE
     * @see Mailcode_Factory::URL_ENCODING_DECODE
     */
    public function setEncoding(Mailcode_Commands_Command $cmd, string $urlEncoding) : void
    {
        // First off, reset the encoding
        $cmd->setURLEncoding(false);
        $cmd->setURLDecoding(false);

        if ($urlEncoding === Mailcode_Factory::URL_ENCODING_ENCODE) {
            $cmd->setURLEncoding();
            return;
        }

        if ($urlEncoding === Mailcode_Factory::URL_ENCODING_DECODE) {
            $cmd->setURLDecoding();
            return;
        }
    }

    /**
     * Quotes a string literal: adds the quotes, and escapes any quotes already present in it.
     *
     * @param string $string
     * @return string
     */
    public function quoteString(string $string) : string
    {
        if(substr($string, 0, 1) === '"' && substr($string, -1, 1) === '"') {
            return $string;
        }

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
