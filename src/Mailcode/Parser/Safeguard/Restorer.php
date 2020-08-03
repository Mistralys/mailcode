<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Restorer} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Restorer
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * The restorer is used to determine which strings the
 * safeguard's placeholders have to be made whole with,
 * depending on the specified settings.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Restorer
{
   /**
    * @var Mailcode_Parser_Safeguard
    */
    private $safeguard;
    
   /**
    * @var bool
    */
    private $highlighted;
    
   /**
    * @var bool
    */
    private $normalize;
    
   /**
    * @var array<string,string>
    */
    private $replaces = array();
    
    public function __construct(Mailcode_Parser_Safeguard $safeguard, bool $highlighted, bool $normalize)
    {
        $this->safeguard = $safeguard;
        $this->highlighted = $highlighted;
        $this->normalize = $normalize;
    }
    
   /**
    * @return array<string,string>
    */
    public function getReplaces() : array
    {
        $placeholders = $this->safeguard->getPlaceholders();
        
        $this->replaces = array();
        
        foreach($placeholders as $placeholder)
        {
            $this->processPlaceholder($placeholder);
        }
        
        return $this->replaces;
    }
    
    private function processPlaceholder(Mailcode_Parser_Safeguard_Placeholder $placeholder) : void
    {
        $replace = '';
        $needle = $placeholder->getReplacementText();
        
        if($this->highlighted)
        {
            $replace = $this->processPlaceholder_highlighted($placeholder, $needle);
        }
        else if($this->normalize)
        {
            $replace = $placeholder->getNormalizedText();
        }
        else
        {
            $replace = $placeholder->getOriginalText();
        }
        
        $this->replaces[$needle] = $replace;
    }
    
    private function processPlaceholder_highlighted(Mailcode_Parser_Safeguard_Placeholder $placeholder, string $needle) : string
    {
        $formatter = $this->safeguard->getFormatter();
        
        if(!$formatter)
        {
            return $placeholder->getHighlightedText();
        }
        
        $formattedNeedle = $formatter->getReplaceNeedle($placeholder);
        
        if($formattedNeedle !== $needle)
        {
            $this->replaces[$formattedNeedle] = $placeholder->getHighlightedText();
            
            return $placeholder->getNormalizedText();
        }
        
        return $placeholder->getHighlightedText();
    }
}
