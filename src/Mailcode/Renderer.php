<?php
/**
 * File containing the {@see Mailcode_Renderer} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Renderer
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to easily create commands and convert them to strings,
 * either in plain text or highlighted.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Renderer
{
   /**
    * @var boolean
    */
    protected $highlighted = false;
    
   /**
    * Sets whether to output highlighted commands instead of the default plain text.
    * 
    * @param bool $highlighted
    * @return Mailcode_Renderer
    */
    public function setOutputHighlighted(bool $highlighted=true) : Mailcode_Renderer
    {
        $this->highlighted = $highlighted;
        
        return $this;
    }
    
   /**
    * Converts a show variable command to string.
    * 
    * @param string $variableName The variable name, with or without $ sign.
    * @return string
    */
    public function showVar(string $variableName) : string
    {
        return $this->command2string(Mailcode_Factory::show()->var($variableName));
    }

    public function showSnippet(string $snippetName) : string
    {
        return $this->command2string(Mailcode_Factory::show()->snippet($snippetName));
    }
    
    public function setVar(string $variableName, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::set()->var($variableName, $value, $quoteValue));
    }
    
    public function setVarString(string $variableName, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::set()->varString($variableName, $value));
    }
    
    public function if(string $condition, string $type='') : string
    {
        return $this->command2string(Mailcode_Factory::if()->if($condition, $type));
    }
    
    public function ifVar(string $variable, string $operand, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::if()->var($variable, $operand, $value, $quoteValue));
    }

    public function ifVarString(string $variable, string $operand, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::if()->varString($variable, $operand, $value));
    }
    
    public function ifVarEquals(string $variable, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::if()->varEquals($variable, $value, $quoteValue));
    }

    public function ifVarEqualsString(string $variable, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::if()->varEqualsString($variable, $value));
    }
    
    public function ifVarNotEquals(string $variable, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::if()->varNotEquals($variable, $value, $quoteValue));
    }

    public function ifVarNotEqualsString(string $variable, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::if()->varNotEqualsString($variable, $value));
    }
    
    public function elseIf(string $condition, string $type='') : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->elseIf($condition, $type));
    }
    
    public function elseIfVar(string $variable, string $operand, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->var($variable, $operand, $value, $quoteValue));
    }

    public function elseIfVarString(string $variable, string $operand, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->varString($variable, $operand, $value));
    }
    
    public function elseIfVarEquals(string $variable, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->varEquals($variable, $value, $quoteValue));
    }

    public function elseIfVarEqualsString(string $variable, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->varEqualsString($variable, $value));
    }
    
    public function elseIfVarNotEquals(string $variable, string $value, bool $quoteValue=false) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->varNotEquals($variable, $value, $quoteValue));
    }

    public function elseIfVarNotEqualsString(string $variable, string $value) : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->varNotEqualsString($variable, $value));
    }
    
    public function else() : string
    {
        return $this->command2string(Mailcode_Factory::elseIf()->else());
    }
    
    public function end() : string
    {
        return $this->command2string(Mailcode_Factory::misc()->end());
    }
    
    public function comment(string $comment) : string
    {
        return $this->command2string(Mailcode_Factory::misc()->comment($comment));
    }
    
    protected function command2string(Mailcode_Commands_Command $command) : string
    {
        if($this->highlighted)
        {
            return $command->getHighlighted();
        }
        
        return $command->getNormalized();
    }
}
