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
        $this->appendParams();
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
    
    protected function appendParams() : void
    {
        if($this->command->hasParameters())
        {
            $this->parts[] = ' '.$this->renderTag(array('params'), $this->command->getParamsString());
        }
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
