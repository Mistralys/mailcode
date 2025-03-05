<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL\Base;

use Mailcode\Commands\ParamsException;
use Mailcode\Mailcode_Commands_IfBase;
use Mailcode\Mailcode_Commands_LogicKeywords_Keyword;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Variables_Variable;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Abstract base class for the IF/ELSEIF command translation classes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class AbstractIfBase extends BaseHubLCommandTranslation
{
    public const ERROR_CANNOT_GET_KEYWORD_SIGN = 60801;
    public const ERROR_INVALID_KEYWORD_COMMAND_TYPE = 60802;
    
    abstract protected function getCommandTemplate() : string;

    protected function getIfType(Mailcode_Commands_IfBase $command) : string
    {
        $parts = explode('_', get_class($command));

        return array_pop($parts);
    }

    protected function translateBody(Mailcode_Commands_IfBase $command) : string
    {
        // The command's getID() method will return "If" for all flavors
        // of the command. We use a custom method to determine the actual
        // IF type.
        $method = 'translate'.$this->getIfType($command);

        if(method_exists($this, $method))
        {
            return (string)$this->$method($command);
        }

        return '';
    }

    /**
     * @param Mailcode_Commands_IfBase $command
     * @return string
     * @throws Mailcode_Exception
     * @throws ParamsException
     */
    protected function _translate(Mailcode_Commands_IfBase $command): string
    {
        $body = $this->translateBody($command);

        $keywords = $command->getLogicKeywords()->getKeywords();
        
        foreach($keywords as $keyword)
        {
            $keyCommand = $keyword->getCommand();
            
            if($keyCommand instanceof Mailcode_Commands_IfBase)
            {
                $body .= ' '.$this->getSign($keyword).' '.$this->translateBody($keyCommand);
            }
            else
            {
                throw new Mailcode_Exception(
                    'Keyword command type does not match expected base class.',
                    sprintf(
                        'Expected instance of [%s], got [%s].',
                        Mailcode_Commands_IfBase::class,
                        get_class($keyCommand)
                    ),
                    self::ERROR_INVALID_KEYWORD_COMMAND_TYPE
                );
            }
        }
        
        return sprintf($this->getCommandTemplate(), $body);
    }

    /**
     * @param Mailcode_Commands_LogicKeywords_Keyword $keyword
     * @return string
     * @throws Mailcode_Exception
     */
    protected function getSign(Mailcode_Commands_LogicKeywords_Keyword $keyword) : string
    {
        switch($keyword->getName())
        {
            case 'and':
                return 'and';
                
            case 'or':
                return 'or';
        }
        
        throw new Mailcode_Exception(
            'Unknown keyword name',
            sprintf(
                'The keyword name [%s] is not known and cannot be translated to a HubL sign.',
                $keyword->getName()
            ),
            self::ERROR_CANNOT_GET_KEYWORD_SIGN
        );
    }

    protected function _translateNumberComparison(Mailcode_Variables_Variable $variable, float $value, string $comparator) : string
    {
        return sprintf(
            '%1$s %2$s %3$s',
            $this->renderStringToNumber($variable->getFullName()),
            $comparator,
            $value
        );
    }

    protected function _translateEmpty(Mailcode_Variables_Variable $variable, bool $notEmpty) : string
    {
        $sign = '!';
        
        if($notEmpty)
        {
            $sign = '';
        }
        
        return sprintf(
            '%s%s',
            $sign,
            $this->formatVariableName($variable->getFullName())
        );
    }
    
    protected function _translateGeneric(Mailcode_Commands_IfBase $command) : string
    {
        $params = $command->getParams();

        if(!$params)
        {
            return '';
        }

        if($command->hasFreeformParameters())
        {
            return $params->getStatementString();
        }

        return $params->getNormalized();
    }
    
    protected function _translateVariable(Mailcode_Variables_Variable $variable, string $comparator, string $value, bool $insensitive=false) : string
    {
        $fullName =  $this->formatVariableName($variable->getFullName());

        if($insensitive)
        {
            $fullName .= '|lower';
            $value = mb_strtolower($value);
        }

        return sprintf(
            '%s %s %s',
            $fullName,
            $comparator,
            $value
        );
    }

    /**
     * @param Mailcode_Variables_Variable $variable
     * @param bool $caseSensitive
     * @param bool $regexEnabled
     * @param Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[] $searchTerms
     * @param string $containsType
     * @return string
     * @throws Mailcode_Exception
     */
    protected function _translateContains(Mailcode_Variables_Variable $variable, bool $caseSensitive, bool $regexEnabled, array $searchTerms, string $containsType) : string
    {
        return $this->translateNotImplemented();
    }

    protected function translateNotImplemented(): string
    {
        return '{# ! if commands are not fully implemented ! #}';
    }

    protected function _translateSearch(string $mode, Mailcode_Variables_Variable $variable, bool $caseSensitive, string $searchTerm) : string
    {
        return $this->translateNotImplemented();
    }
}
