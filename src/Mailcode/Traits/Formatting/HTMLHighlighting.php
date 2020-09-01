<?php
/**
 * File containing the {@see Mailcode_Traits_Formatting_HTMLHighlighting} trait.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Traits_Formatting_HTMLHighlighting
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Trait for HTML Highlighting formatters. This is a 
 * good solution to share this functionality between
 * different types (like the MarkVariables and HTMLHighlighting
 * formatters, since they extend different classes).
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
trait Mailcode_Traits_Formatting_HTMLHighlighting
{
    /**
     * @var string[]
     */
    private $excludeTags = array(
        'style', // NOTE: style tags are excluded natively on the parser level.
        'script'
    );
    
   /**
    * Adds an HTML tag name to the list of tags within which
    * commands may not be highlighted.
    *
    * @param string $tagName Case insensitive.
    * @return Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    */
    public function excludeTag(string $tagName) : Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    {
        $tagName = strtolower($tagName);
        
        if(!in_array($tagName, $this->excludeTags))
        {
            $this->excludeTags[] = $tagName;
        }
        
        return $this;
    }
    
   /**
    * Adds several exluded tag names at once.
    *
    * @param string[] $tagNames
    * @return Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    */
    public function excludeTags(array $tagNames) : Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    {
        foreach($tagNames as $tagName)
        {
            $this->excludeTag((string)$tagName);
        }
        
        return $this;
    }
    
   /**
    * Whether the specified tag name is in the exlusion list.
    *
    * @param string $tagName
    * @return bool
    */
    public function isTagExcluded(string $tagName) : bool
    {
        $tagName = strtolower($tagName);
        
        return in_array($tagName, $this->excludeTags);
    }
    
    public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location): string
    {
        return $location->getPlaceholder()->getHighlightedText();
    }
}
