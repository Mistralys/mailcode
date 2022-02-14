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

/**
 * Detects whether the placeholder location can be highlighted or not.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting_Location extends Mailcode_Parser_Safeguard_Formatter_Location
{
    public const COMMAND_MARKER = '__MAILCODE_COMMAND__';
    
   /**
    * @var boolean
    */
    private $ancestryChecked = false;
    
   /**
    * @var array<int,array<int,string>>
    */
    private $tagAncestry = array();
    
    protected function init() : void
    {
    }
    
    public function requiresAdjustment(): bool
    {
        return !$this->isInTagAttributes() && !$this->isInExcludedTag();
    }
    
   /**
    * Retrieves the part of the subject string that comes
    * before the command.
    * 
    * @return string
    */
    private function getHaystackBefore() : string
    {
        $pos = $this->getStartPosition();
        
        if(is_bool($pos))
        {
            return '';
        }
        
        // We add a command marker and a closing tag bracket,
        // so that if the command is in a tag's attributes,
        // the tags ancestry can detect the tag as a parent 
        // tag, including the marker in the attributes string.
        return $this->subject->getSubstr(0, $pos).self::COMMAND_MARKER.'>';
    }
    
   /**
    * Whether the command is nested in one of the tags
    * that have been added to the list of excluded tags.
    * 
    * @return bool
    */
    protected function isInExcludedTag() : bool
    {
        $tagNames = $this->getParentTags();
        
        foreach($tagNames as $tagName)
        {
            if($this->formatter->isTagExcluded($tagName))
            {
                return true;
            }
        }
        
        return false;
    }
    
   /**
    * Retrieves a list of the command's parent HTML tags, from
    * highest to lowest.
    * 
    * @return string[]
    */
    private function getParentTags() : array
    {
        $tags = $this->getTagAncestry();
        
        // Create a stack of all direct parent tags of the command.
        // Handles closing tags as well.
        $stack = array();
        foreach($tags[2] as $idx => $tagName)
        {
            $closing = $tags[1][$idx] === '/';
            
            if($closing)
            {
                array_pop($stack);
            }
            else
            {
                $stack[] = $tagName;
            }
        }
        
        return $stack;
    }
    
   /**
    * Checks whether the command is located within the attributes
    * of an HTML tag.
    * 
    * @return bool
    */
    protected function isInTagAttributes() : bool
    {
        $tags = $this->getTagAncestry();
        
        // This check is easy: if the command is in the attribute
        // of any of the tags, we will find the command marker in there.
        foreach($tags[3] as $attributes)
        {
            if(strstr($attributes, self::COMMAND_MARKER))
            {
                return true;
            }
        }
        
        return false;
    }
    
   /**
    * Retrieves a list of tag names that come as parents 
    * above the command.
    * 
    * @return array<int,array<int,string>>
    */
    private function getTagAncestry() : array
    {
        if($this->ancestryChecked)
        {
            return $this->tagAncestry;
        }
        
        $this->ancestryChecked = true;
        
        $haystack = $this->getHaystackBefore();
        
        // Get a list of all HTML tags before the command, opening and closing.
        $matches = array();
        preg_match_all('%<\s*(/?)\s*([a-z][a-z0-9]*)\s*([^<>]*)>%ix', $haystack, $matches, PREG_PATTERN_ORDER);
        
        $this->tagAncestry = $matches;
        
        return $matches;
    }
}
