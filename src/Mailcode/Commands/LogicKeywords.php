<?php
/**
 * @package Mailcode
 * @subpackage Commands
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\OperationResult;

/**
 * Handles parsing logic keywords in commands, if any.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Commands_Command $subject
 */
class Mailcode_Commands_LogicKeywords extends OperationResult
{
    public const ERROR_CANNOT_APPEND_INVALID_KEYWORD = 60501;
    public const ERROR_KEYWORD_MATCHED_STRING_NOT_FOUND = 60502;
    
    public const VALIDATION_CANNOT_MIX_LOGIC_KEYWORDS = 60701;
    public const VALIDATION_INVALID_SUB_COMMAND = 60702;
    public const ERROR_KEYWORD_SUBSTRING_NOT_FOUND = 60503;

    private string $paramsString;
    
   /**
    * @var string[]
    */
    private array $names = array(
        'and', 
        'or'
    );
    
   /**
    * @var Mailcode_Commands_LogicKeywords_Keyword[]
    */
    private array $keywords = array();
    
    private string $mainParams = '';
    
    public function __construct(Mailcode_Commands_Command $command, string $paramsString)
    {
        parent::__construct($command);
        
        $this->paramsString = $paramsString;
        
        $this->parse();
        $this->validate();
    }
    
    public function getCommand() : Mailcode_Commands_Command
    {
        return $this->subject;
    }
    
    private function parse() : void
    {
        foreach($this->names as $name)
        {
            if(stripos($this->paramsString, $name) === false) {
                continue;
            }

            array_push(
                $this->keywords,
                ...$this->detectKeywords($name)
            );
        }
    }
    
    private function validate() : void
    {
        $names = $this->getDetectedNames();
        $amount = count($names);
        
        if($amount > 1)
        {
            $this->makeError(
                t(
                    'Cannot mix the logical keywords %1$s:',
                    ConvertHelper::implodeWithAnd($names, ', ', ' '.t('and').' ')
                ).' '.
                t('Only one flavor of keyword may be used within the same command.').' '.
                t('To illustrate, you may use the %1$s keyword several times, but not the %1$s keyword and %2$s keyword at the same time.', '"and"', '"or"'),
                self::VALIDATION_CANNOT_MIX_LOGIC_KEYWORDS
            );
            
            return;
        }
        
        $this->splitParams();
    }
    
    private function splitParams() : void
    {
        if(empty($this->keywords))
        {
            $this->mainParams = $this->paramsString;
            
            return;
        }
        
        $params = $this->detectParameters();
        
        foreach($this->keywords as $keyword)
        {
            $kParams = (string)array_shift($params);
            
            $keyword->setParamsString($kParams);
            
            if(!$keyword->isValid())
            {
                $this->makeError(
                    t('Error #%1$s:', $keyword->getCode()).' '.$keyword->getErrorMessage(),
                    self::VALIDATION_INVALID_SUB_COMMAND
                );
                
                return;
            }
        }
    }
    
   /**
    * @return string[]
    */
    private function detectParameters() : array
    {
        $params = $this->paramsString;
        $stack = array();
        
        foreach($this->keywords as $keyword)
        {
            $params = $this->detectParamsKeyword($params, $keyword, $stack);
        }
        
        $stack[] = $params;
        
        $this->mainParams = (string)array_shift($stack);
        
        return $stack;
    }

   /**
    * @param string $params
    * @param Mailcode_Commands_LogicKeywords_Keyword $keyword
    * @param string[] $stack
    * @throws Mailcode_Exception
    * @return string
    */
    private function detectParamsKeyword(string $params, Mailcode_Commands_LogicKeywords_Keyword $keyword, array &$stack) : string
    {
        $search = $keyword->getMatchedString();
        $pos = strpos($params, $search, 0);
        
        if($pos === false)
        {
            throw new Mailcode_Exception(
                'Keyword matched string not found',
                null,
                self::ERROR_KEYWORD_MATCHED_STRING_NOT_FOUND
            );
        }
        
        $length = strlen($search);
        
        $store = substr($params, 0, $pos);

        if($store === false)
        {
            throw new Mailcode_Exception(
                'Keyword substring not found',
                null,
                self::ERROR_KEYWORD_SUBSTRING_NOT_FOUND
            );
        }

        $params = trim(substr($params, $pos+$length));
        
        $stack[] = $store;
        
        return $params;
    }
    
   /**
    * Extracts the parameters string to use for the 
    * original command itself, omitting all the logic
    * keywords for the sub-commands.
    * 
    * @return string
    */
    public function getMainParamsString() : string
    {
        return $this->mainParams;
    }
    
   /**
    * Retrieves the detected keyword names.
    * @return string[]
    */
    public function getDetectedNames() : array
    {
        $names = array();
        
        foreach($this->keywords as $keyword)
        {
            $name = $keyword->getName();
            
            if(!in_array($name, $names, true)) {
                $names[] = $name;
            }
        }
        
        return $names;
    }
    
   /**
    * Retrieves all keywords detected in the
    * command's parameter string, if any.
    * 
    * @return Mailcode_Commands_LogicKeywords_Keyword[]
    */
    public function getKeywords() : array
    {
        return $this->keywords;
    }
    
   /**
    * Detects any keyword statements in the parameters by keyword name.
    * 
    * @param string $name
    * @return Mailcode_Commands_LogicKeywords_Keyword[]
    */
    private function detectKeywords(string $name) : array
    {
        $regex = sprintf('/%1$s\s+([a-z\-0-9]+):|%1$s:/x', $name);
        
        $matches = array();
        preg_match_all($regex, $this->paramsString, $matches, PREG_PATTERN_ORDER);
        
        if(empty($matches[0][0])) {
            return array();
        }
        
        $amount = count($matches[0]);
        $result = array();
        
        for($i=0; $i < $amount; $i++)
        {
            $result[] = $this->createKeyword(
                $name, 
                $matches[1][$i],
                $matches[0][$i] 
            );
        }
        
        return $result;
    }
    
    public function hasKeywords() : bool
    {
        return !empty($this->keywords);
    }
    
    public function appendAND(string $paramsString, string $type='') : Mailcode_Commands_LogicKeywords_Keyword
    {
        return $this->appendKeyword('and', $paramsString, $type);
    }
    
    public function appendOR(string $paramsString, string $type='') : Mailcode_Commands_LogicKeywords_Keyword
    {
        return $this->appendKeyword('or', $paramsString, $type);
    }
    
    public function appendKeyword(string $name, string $paramsString, string $type='') : Mailcode_Commands_LogicKeywords_Keyword
    {
        $keyword = $this->createKeyword($name, $type);
        $keyword->setParamsString($paramsString);
        
        if(!$keyword->isValid())
        {
            throw new Mailcode_Exception(
                'Cannot append invalid logic keyword',
                sprintf(
                    'The keyword [%s] cannot be added with parameters [%s] and type [%s]: it is invalid. Validation details: #%s %s',
                    $name,
                    $paramsString,
                    $type,
                    $keyword->getCode(),
                    $keyword->getErrorMessage()
                ),
                self::ERROR_CANNOT_APPEND_INVALID_KEYWORD
            );
        }
        
        $this->keywords[] = $keyword;
        
        return $keyword;
    }
    
    private function createKeyword(string $name, string $type='', string $matchedString='') : Mailcode_Commands_LogicKeywords_Keyword
    {
        if(empty($matchedString))
        {
            $matchedString = $name;
            
            if(!empty($type))
            {
                $matchedString .= ' '.$type;
            }
            
            $matchedString .= ':';
        }
        
        return new Mailcode_Commands_LogicKeywords_Keyword(
            $this,
            $name,
            $matchedString,
            $type
        );
    }
}
