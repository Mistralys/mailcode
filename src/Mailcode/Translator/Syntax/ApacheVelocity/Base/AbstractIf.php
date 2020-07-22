<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_Base_AbstractIf} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_Base_AbstractIf
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for the IF/ELSEIF command translation classes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Translator_Syntax_ApacheVelocity_Base_AbstractIf extends Mailcode_Translator_Syntax_ApacheVelocity
{
    const ERROR_CANNOT_GET_KEYWORD_SIGN = 60801;
    const ERROR_INVALID_KEYWORD_COMMAND_TYPE = 60802;
    
    abstract protected function getCommandTemplate() : string;

    abstract protected function translateBody(Mailcode_Commands_IfBase $command) : string;
    
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
    
    protected function getSign(Mailcode_Commands_LogicKeywords_Keyword $keyword) : string
    {
        switch($keyword->getName())
        {
            case 'and':
                return '&&';
                
            case 'or':
                return '||';
        }
        
        throw new Mailcode_Exception(
            'Unknown keyword name',
            sprintf(
                'The keyword name [%s] is not known and cannot be translated to a velocity sign.',
                $keyword->getName()
            ),
            self::ERROR_CANNOT_GET_KEYWORD_SIGN
        );
    }
    
    protected function _translateEmpty(Mailcode_Variables_Variable $variable, bool $notEmpty) : string
    {
        $sign = '';
        
        if($notEmpty)
        {
            $sign = '!';
        }
        
        return sprintf(
            '%s$StringUtils.isEmpty(%s)',
            $sign,
            $variable->getFullName()
        );
    }
    
    protected function _translateGeneric(Mailcode_Commands_IfBase $command) : string
    {
        $params = $command->getParams();
        
        if(!$params)
        {
            return '';
        }
        
        return $params->getNormalized();
    }
    
    protected function _translateVariable(Mailcode_Variables_Variable $variable, string $comparator, string $value) : string
    {
        return sprintf(
            '%s %s %s',
            $variable->getFullName(),
            $comparator,
            $value
        );
    }
    
    protected function _translateContains(Mailcode_Variables_Variable $variable, bool $caseSensitive, string $searchTerm) : string
    {
        $opts = 's';
        if($caseSensitive)
        {
            $opts = 'is';
        }
        
        return sprintf(
            '%s.matches("(?%s)%s")',
            $variable->getFullName(),
            $opts,
            $this->filterRegexString(trim($searchTerm, '"'))
        );
    }
    
    protected function _translateSearch(string $mode, Mailcode_Variables_Variable $variable, bool $caseSensitive, string $searchTerm) : string
    {
        $method = $mode.'With';
        if($caseSensitive)
        {
            $method = $mode.'WithIgnoreCase';
        }
        
        return sprintf(
            '$StringUtils.%s(%s, "%s")',
            $method,
            $variable->getFullName(),
            trim($searchTerm, '"')
        );
    }
}
