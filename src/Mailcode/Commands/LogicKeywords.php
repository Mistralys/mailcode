<?php
/**
 * File containing the {@see Mailcode_Commands_Command} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command
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
    const ERROR_CANNOT_APPEND_INVALID_KEYWORD = 60501;
    
    const VALIDATION_CANNOT_MIX_LOGIC_KEYWORDS = 60701;
    const VALIDATION_INVALID_SUB_COMMAND = 60702;
    
   /**
    * @var string
    */
    private $paramsString;
    
   /**
    * @var string[]
    */
    private $names = array(
        'and', 
        'or'
    );
    
   /**
    * @var Mailcode_Commands_LogicKeywords_Keyword[]
    */
    private $keywords = array();
    
   /**
    * @var string
    */
    private $mainParams = '';
    
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
            if(!stristr($this->paramsString, $name))
            {
                continue;
            }
            
            $this->keywords = array_merge(
                $this->keywords, 
                $this->detectKeywords($name)
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
                t('Only one keyword may be used within the same command.'),
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
            $kParams = array_shift($params);
            
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
    
    private function detectParameters() : array
    {
        $params = $this->paramsString;
        $stack = array();
        
        foreach($this->keywords as $keyword)
        {
            $search = $keyword->getMatchedString();
            $pos = strpos($params, $search);
            $length = strlen($search);
            
            $store = substr($params, 0, $pos);
            $params = trim(substr($params, $pos+$length));
            
            $stack[] = $store;
        }
        
        $stack[] = $params;
        
        $this->mainParams = array_shift($stack);
        
        return $stack;
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
            
            if(!in_array($name, $names))
            {
                $names[] = $name;
            }
        }
        
        return $names;
    }
    
   /**
    * Retrieves all keywords that were detected in the
    * command's parameters string, if any.
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
        
        if(!isset($matches[0][0]) || empty($matches[0][0]))
        {
            return array();
        }
        
        $amount = count($matches[0]);
        
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