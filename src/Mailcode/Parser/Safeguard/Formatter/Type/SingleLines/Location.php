<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines_Placeholder} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines_Placeholder
 */

declare(strict_types=1);

namespace Mailcode;

use function AppUtils\parseVariable;

/**
 * Detects whether the placeholder needs newlines characters
 * prepended or appended.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Safeguard_Formatter_Type_SingleLines $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_Type_SingleLines_Location extends Mailcode_Parser_Safeguard_Formatter_Location
{
   /**
    * @var int
    */
    protected $eolLength;
    
   /**
    * @var string
    */
    protected $eol;
    
    protected function init() : void
    {
        $this->eolLength = $this->formatter->getEOLLength();
        $this->eol = $this->formatter->getEOLChar();
    
        $this->analyzePrepend();
        $this->analyzeAppend();
    }
    
    public function requiresAdjustment(): bool
    {
        return !$this->placeholder->getCommand()->generatesContent();
    }
    
    protected function analyzePrepend() : void
    {
        $position = $this->getStartPosition();
        
        // we're at the beginning of the string
        if($position === false || $position === 0)
        {
            return;
        }
        
        $prevPos = $position - $this->eolLength;
        
        if($prevPos < 0)
        {
            $prevPos = 0;
        }
        
        if($this->isWithinCommand($prevPos))
        {
            return;
        }
        
        $prev = $this->subject->getSubstr($prevPos, $this->eolLength);
        
        if($prev !== $this->formatter->getEOLChar())
        {
            $this->prepend = $this->eol;
        }
    }
    
    protected function analyzeAppend() : void
    {
        $subjectLength = $this->subject->getLength();
        
        $position = $this->getEndPosition();
        
        // we're at the end of the string
        if($position === false || $position >= $subjectLength)
        {
            return;
        }
        
        $nextPos = $position + $this->eolLength;
        
        if($nextPos > $subjectLength)
        {
            $nextPos = $subjectLength - $this->eolLength;
        }
        
        $next = $this->subject->getSubstr($nextPos, $this->eolLength);
        
        if($next !== $this->formatter->getEOLChar())
        {
            $this->append = $this->eol;
        }
    }
}
