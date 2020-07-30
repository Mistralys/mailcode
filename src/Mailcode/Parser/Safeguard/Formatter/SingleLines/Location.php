<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_SingleLines_Placeholder} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_SingleLines_Placeholder
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Detects whether the placeholder needs newlines characters
 * prepended or appended.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Safeguard_Formatter_SingleLines $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_SingleLines_Location extends Mailcode_Parser_Safeguard_FormatterLocation
{
   /**
    * @var int
    */
    protected $eolLength;
    
   /**
    * @var string
    */
    protected $eol;
    
   /**
    * @var boolean
    */
    protected $prepend = false;

   /**
    * @var boolean
    */
    protected $append = false;
    
    protected function init() : void
    {
        $this->eolLength = $this->formatter->getEOLLength();
        $this->eol = $this->formatter->getEOLChar();
    
        $this->analyzePrepend();
        $this->analyzeAppend();
    }
    
   /**
    * Whether an EOL character needs to be appended or prepended.
    *  
    * @return bool
    */
    public function requiresAdjustment() : bool
    {
        return $this->requiresAppend() || $this->requiresPrepend();
    }
    
    public function requiresPrepend() : bool
    {
        return $this->prepend;
    }
    
    public function requiresAppend() : bool
    {
        return $this->append;
    }
    
    protected function analyzePrepend() : void
    {
        $position = $this->location->getStartPosition();
        
        // we're at the beginning of the string
        if($position == 0)
        {
            return;
        }
        
        $prevPos = $position - $this->eolLength;
        
        if($prevPos < 0)
        {
            $prevPos = 0;
        }
        
        $prev = mb_substr($this->location->getSubjectString(), $prevPos, $this->eolLength);
        
        if($prev !== $this->formatter->getEOLChar())
        {
            $this->prepend = true;
        }
    }
    
    protected function analyzeAppend() : void
    {
        $subjectLength = $this->location->getSubjectLength();
        
        $position = $this->location->getEndPosition();
        
        // we're at the end of the string
        if($position >= $subjectLength)
        {
            return;
        }
        
        $nextPos = $position + $this->eolLength;
        
        if($nextPos > $subjectLength)
        {
            $nextPos = $subjectLength - $this->eolLength;
        }
        
        $next = mb_substr($this->location->getSubjectString(), $nextPos, $this->eolLength);
        
        if($next !== $this->formatter->getEOLChar())
        {
            $this->append = true;
        }
    }
    
    protected function getAdjustedText() : string
    {
        $prepend = '';
        $append = '';
        
        if($this->requiresPrepend())
        {
            $prepend = $this->eol;
        }
        
        if($this->requiresAppend())
        {
            $append = $this->eol;
        }
        
        return $prepend.$this->location->getPlaceholderString().$append;
    }
}
