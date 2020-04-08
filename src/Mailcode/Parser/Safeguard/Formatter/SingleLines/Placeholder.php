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
 */
class Mailcode_Parser_Safeguard_Formatter_SingleLines_Placeholder
{
   /**
    * @var int
    */
    protected $eolLength;
    
   /**
    * @var int
    */
    protected $position;

   /**
    * @var string
    */
    protected $subject;
    
   /**
    * @var Mailcode_Parser_Safeguard_Formatter_SingleLines
    */
    protected $formatter;
    
   /**
    * @var boolean
    */
    protected $prepend = false;

   /**
    * @var boolean
    */
    protected $append = false;
    
   /**
    * @var int
    */
    protected $placeholderLength;
    
    public function __construct(Mailcode_Parser_Safeguard_Formatter_SingleLines $formatter, string $subject, int $placeholderLength, int $position)
    {
        $this->formatter = $formatter;
        $this->eolLength = $formatter->getEOLLength();
        $this->position = $position;
        $this->placeholderLength = $placeholderLength; 
    
        $this->analyzePrepend($subject);
        $this->analyzeAppend($subject);
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
    
    protected function analyzePrepend(string $subject) : void
    {
        // we're at the beginning of the string
        if($this->position == 0)
        {
            return;
        }
        
        $prevPos = $this->position - $this->eolLength;
        
        if($prevPos < 0)
        {
            $prevPos = 0;
        }
        
        $prev = mb_substr($subject, $prevPos, $this->eolLength);
        
        if($prev !== $this->formatter->getEOLChar())
        {
            $this->prepend = true;
        }
    }
    
    protected function analyzeAppend(string $subject) : void
    {
        $subjectLength = mb_strlen($subject);
        
        $position = $this->position + $this->placeholderLength;
        
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
        
        $next = mb_substr($subject, $nextPos, $this->eolLength);
        
        if($next !== $this->formatter->getEOLChar())
        {
            $this->append = true;
        }
    }
}
