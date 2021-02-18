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
    * Stored this way so we can use isset() instead
    * of using in_array, which is some magnitudes slower.
    * The boolean value is not used otherwise.
    *
    * @var array<string,bool>
    */
    private $excludeTags = array(
        'style' => true, // NOTE: style tags are excluded natively on the parser level.
        'script' => true
    );
    
   /**
    * Adds an HTML tag name to the list of tags within which
    * commands may not be highlighted.
    *
    * @param string $tagName Case insensitive.
    * @return $this
    */
    public function excludeTag(string $tagName)
    {
        $tagName = strtolower($tagName);
        
        $this->excludeTags[$tagName] = true;

        return $this;
    }
    
   /**
    * Adds several exluded tag names at once.
    *
    * @param string[] $tagNames
    * @return $this
    */
    public function excludeTags(array $tagNames)
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
        
        return isset($this->excludeTags[$tagName]);
    }
    
    public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location): string
    {
        return $location->getPlaceholder()->getHighlightedText();
    }
}
