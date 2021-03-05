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
class Mailcode_Commands_Highlighter
{
   /**
    * @var Mailcode_Commands_Command
    */
    protected $command;
    
   /**
    * @var string[]
    */
    protected $parts = array();
    
    public function __construct(Mailcode_Commands_Command $command)
    {
       $this->command = $command;
    }
    
    public function highlight() : string
    {
        $this->parts = array();
        
        $this->appendBracket('{');
        $this->appendCommand();
        $this->appendParams($this->command);
        $this->appendLogicKeywords();
        $this->appendBracket('}');
        
        return implode('', $this->parts);
    }
    
    protected function appendCommand() : void
    {
        $this->parts[] = $this->renderTag(array('command-name'), $this->command->getName());
        
        if($this->command->hasType())
        {
            $this->parts[] = ' '.$this->renderTag(array('command-type'), $this->command->getType());
        }
        
        if($this->command->requiresParameters())
        {
            $this->parts[] = $this->renderTag(array('hyphen'), ':');
            $this->parts[] = '<wbr>';
        }
    }
    
    protected function appendParams(Mailcode_Commands_Command $command) : void
    {
        $params = $command->getParams();
        
        if($params === null)
        {
            return;
        }
        
        $tokens = $params->getInfo()->getTokens();

        if(!empty($tokens))
        {
            $this->parts[] = '<span class="mailcode-params">';

            foreach ($tokens as $token)
            {
                $this->appendParamToken($token);
            }

            $this->parts[] = '</span>';
        }
    }
    
    protected function appendParamToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->parts[] = ' '.$this->renderTag(array('token-'.strtolower($token->getTypeID())), $token->getNormalized());
    }
    
    protected function appendLogicKeywords() : void
    {
        if(!$this->command->supportsLogicKeywords())
        {
            return;
        }
        
        $keywords = $this->command->getLogicKeywords()->getKeywords();
        
        foreach($keywords as $keyword)
        {
            $this->appendLogicKeyword($keyword);
        }
    }
    
    protected function appendLogicKeyword(Mailcode_Commands_LogicKeywords_Keyword $keyword) : void
    {
        $this->parts[] = ' '.$this->renderTag(array('logic-'.$keyword->getName()), $keyword->getName());
        
        $type = $keyword->getType();
        
        if(!empty($type))
        {
            $this->parts[] = ' '.$this->renderTag(array('command-type'), $type);
        }
        
        $this->parts[] = $this->renderTag(array('hyphen'), ':');
        
        $this->appendParams($keyword->getCommand());
    }
    
   /**
    * @param string[] $classes
    * @param string $content
    * @return string
    */
    protected function renderTag(array $classes, string $content) : string
    {
        $parts = array();
        
        foreach($classes as $class)
        {
            $parts[] = 'mailcode-'.$class;
        }
        
        return sprintf(
            '<span class="%s">%s</span>',
            implode(' ', $parts),
            $content
        );
    }
    
    protected function appendBracket(string $bracket) : void
    {
        $this->parts[] = $this->renderTag(
            array('bracket'),
            $bracket
        );
    }
}
