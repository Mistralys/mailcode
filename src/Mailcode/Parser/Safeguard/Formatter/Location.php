<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract safeguard formatter location: this is where the decision
 * is made whether a specific placeholder instance needs to be 
 * transformed according to the formatter. 
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Safeguard_Formatter_Location
{
    const ERROR_PLACEHOLDER_NOT_FOUND = 66001;
    
   /**
    * @var Mailcode_Parser_Safeguard_Formatter
    */
    protected $formatter;
    
   /**
    * @var string
    */
    protected $append = '';

   /**
    * @var string
    */
    protected $prepend = '';
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder
    */
    protected $placeholder;
    
   /**
    * @var Mailcode_StringContainer
    */
    protected $subject;
    
   /**
    * @var string[]
    */
    protected $log = array();
    
    public function __construct(Mailcode_Parser_Safeguard_Formatter $formatter, Mailcode_Parser_Safeguard_Placeholder $placeholder)
    {
        $this->formatter = $formatter;
        $this->placeholder = $placeholder;
        $this->subject = $formatter->getSubject();
        
        $this->init();
    }
    
    abstract protected function init() : void; 
    
    abstract public function requiresAdjustment() : bool;
    
   /**
    * @return int|boolean
    */
    public function getStartPosition()
    {
        return $this->subject->getSubstrPosition($this->placeholder->getReplacementText());
    }
    
   /**
    * Checks whether the specified position within the string
    * is within another command's placeholder (excluding this
    * location's placeholder).
    * 
    * @param int $position
    * @return bool
    */
    public function isWithinCommand(int $position) : bool
    {
        $placeholders = $this->formatter->getSafeguard()->getPlaceholders();
        
        $placeholderID = $this->placeholder->getID();
        
        foreach($placeholders as $placeholder)
        {
            if($placeholder->getID() === $placeholderID)
            {
                continue;
            }
            
            $start = $this->subject->getSubstrPosition($placeholder->getReplacementText());
            
            if($start === false)
            {
                continue;
            }
            
            $end = $start + $placeholder->getReplacementLength();
            
            if($position >= $start && $position <= $end)
            {
                return true;
            }
        }
        
        return false;
    }
    
   /**
    * @return int|boolean
    */
    public function getEndPosition()
    {
        $start = $this->getStartPosition();
        
        if($start !== false)
        {
            return $start + $this->placeholder->getReplacementLength();
        }
        
        return false;
    }
    
    public function getSubject() : Mailcode_StringContainer
    {
        return $this->subject;
    }
    
    public function getPlaceholder() : Mailcode_Parser_Safeguard_Placeholder
    {
        return $this->placeholder;
    }
    
   /**
    * Replaces the placeholder with the specified replacement text.
    * 
    * @param string $replacementText
    * @throws Mailcode_Exception
    * 
    * @see Mailcode_Parser_Safeguard_Formatter_Location::ERROR_PLACEHOLDER_NOT_FOUND
    */
    public function replaceWith(string $replacementText) : void
    {
        $needle = $this->placeholder->getReplacementText();
        
        if($this->subject->replaceSubstrings($needle, $replacementText))
        {
            return;
        }
        
        throw new Mailcode_Exception(
            'Could not find the placeholder to replace',
            sprintf(
                'The placeholder [%s] was not found in the string.',
                $needle
            ),
            self::ERROR_PLACEHOLDER_NOT_FOUND
        );
    }
    
    public function format() : void
    {
        if($this->requiresAdjustment() && (!empty($this->prepend) || !empty($this->append)))
        {
            $this->replaceWith(sprintf(
                '%s%s%s',
                $this->prepend,
                $this->placeholder->getReplacementText(),
                $this->append
            ));
        }
    }
    
    protected function log(string $message) : void
    {
        $this->log[] = sprintf(
            '%s Formatter | Command [%s] | %s',
            $this->formatter->getID(),
            $this->placeholder->getCommand()->getNormalized(),
            $message
        );
    }
    
   /**
    * Retrieves the location's log messages, if any.
    * @return string[]
    */
    public function getLog() : array
    {
        return $this->log;
    }
}
