<?php
/**
 * File containing the {@see Mailcode_Commands_Highlighter} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Highlighter
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Base command class with the common functionality for all commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Normalizer
{
    /**
     * @var Mailcode_Commands_Command
     */
    protected Mailcode_Commands_Command $command;
    
    /**
     * @var string[]
     */
    protected array $parts = array();
    
    public function __construct(Mailcode_Commands_Command $command)
    {
        $this->command = $command;
    }
    
    public function normalize() : string
    {
        if(!$this->command->isValid())
        {
            return '';
        }

        $this->parts = array();
        
        $this->parts[] = '{'.$this->command->getName();
        
        $this->addType();
        $this->addParams();
        $this->addLogicKeywords();
        
        $this->parts[] = '}';
        
        return implode('', $this->parts);
    }
    
    private function addType() : void
    {
        if(!$this->command->supportsType() || !$this->command->hasType())
        {
            return;
        }
        
        $this->parts[] = ' '.$this->command->getType();
    }
    
    private function addParams() : void
    {
        $params = $this->getParamsString();
        
        if(empty($params))
        {
            return;
        }
        
        $this->parts[] = ': ';
        $this->parts[] = $params;
    }

    protected function getParamsStatement() : ?Mailcode_Parser_Statement
    {
        return $this->command->getParams();
    }

    protected function getParamsString() : string
    {
        $params = $this->getParamsStatement();

        if($params === null)
        {
            return '';
        }

        if($this->command->hasFreeformParameters())
        {
            return $params->getStatementString();
        }

        return $params->getNormalized();
    }

    private function addLogicKeywords() : void
    {
        if(!$this->command->supportsLogicKeywords())
        {
            return;
        }
        
        $keywords = $this->command->getLogicKeywords()->getKeywords();
        
        foreach($keywords as $keyword)
        {
            $this->parts[] = ' ';
            $this->parts[] = $keyword->getKeywordString(); // e.g. "if variable"
            
            $this->addParams();
        }
    }
}
