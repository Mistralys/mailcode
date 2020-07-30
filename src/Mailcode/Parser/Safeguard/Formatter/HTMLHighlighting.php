<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * HTML highlighting formatter: Ensures that commands that are highlighted
 * only in locations where this is possible. Commands nested in tag attributes
 * for example, will be ignored.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting extends Mailcode_Parser_Safeguard_Formatter
{
   /**
    * @var string[]
    */
    private $excludeTags = array(
        'style', // NOTE: style tags are excluded natively on the parser level.
        'script'
    );
    
    protected function initFormatting(string $subject) : string
    {
        return $subject;
    }
    
   /**
    * Adds an HTML tag name to the list of tags within which
    * commands may not be highlighted.
    * 
    * @param string $tagName Case insensitive.
    * @return Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting
    */
    public function excludeTag(string $tagName) : Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting
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
    * @return Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting
    */
    public function excludeTags(array $tagNames) : Mailcode_Parser_Safeguard_Formatter_HTMLHighlighting
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
    
    public function getReplaceNeedle(Mailcode_Parser_Safeguard_Placeholder $placeholder) : string
    {
        return '<mailcode:highlight>'.$placeholder->getReplacementText().'</mailcode:highlight>';
    }
}
