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
use AppUtils\ConvertHelper;

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
            $this->log(sprintf(
                'Prepend: NO | Position: [%s] | Not found, or at beginning of string.', 
                parseVariable($position)->enableType()->toString()
            ));
            
            return;
        }
        
        $prevPos = $position - $this->eolLength;
        
        if($prevPos < 0)
        {
            $prevPos = 0;
        }
        
        $this->checkPreviousPosition($prevPos);
    }
    
    protected function checkPreviousPosition(int $prevPos) : void
    {
        if($this->isWithinCommand($prevPos))
        {
            $this->log(sprintf(
                'Prepend: NO | Position: [%s] | Is within a mailcode command.', 
                $prevPos
            ));
            
            return;
        }
        
        $match = $this->subject->getSubstr($prevPos, $this->eolLength);
        
        if($match !== $this->formatter->getEOLChar())
        {
            $this->log(sprintf(
                'Prepend: YES | Position: [%s] | Characters: [%s] | Do not match the EOL character.',
                $prevPos,
                ConvertHelper::hidden2visible($match)
            ));
            
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
            $this->log(sprintf(
                'Append: NO | Position: [%s] | End of string, or too long | Subject length: [%s]',
                parseVariable($position)->enableType()->toString(),
                $subjectLength
            ));
            
            return;
        }
        
        $nextPos = $position + $this->eolLength;
        
        if($nextPos > $subjectLength)
        {
            $nextPos = $subjectLength - $this->eolLength;
        }
        
        $this->checkNextPosition($nextPos);
    }
    
    protected function checkNextPosition(int $nextPos) : void
    {
        if($this->isWithinCommand($nextPos))
        {
            $this->log(sprintf(
                'Append: YES | Position: [%s] | Is within a mailcode command.',
                $nextPos
            ));
            
            $this->append = $this->eol;
            
            return;
        }
        
        $next = $this->subject->getSubstr($nextPos, $this->eolLength);
        
        if($next !== $this->formatter->getEOLChar())
        {
            $this->log(sprintf(
                'Append: YES | Position: [%s] | Next characters: [%s] | Do not match the EOL character.',
                $nextPos,
                ConvertHelper::hidden2visible($next)
            ));
            
            $this->append = $this->eol;
        }
    }
}
