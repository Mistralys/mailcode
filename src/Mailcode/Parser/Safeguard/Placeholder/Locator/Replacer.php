<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to replace a placeholder at a specific location
 * with another text. The replacement handles inseting the
 * new text, while shifting the positions of all other 
 * instances of the placeholder to account for the change
 * in text length. 
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @see Mailcode_Parser_Safeguard_Placeholder_Locator::replaceWith()
 */
class Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer
{
    const ERROR_PLACEHOLDER_STRING_MISSING = 63501;
    const ERROR_COULD_NOT_FIND_PLACEHOLDER_TEXT = 63502;
    
   /**
    * @var string
    */
    private $replacementText;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder_Locator
    */
    private $locator;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder_Locator_Location
    */
    private $origin;
    
   /**
    * @var string
    */
    private $placeholderText;
    
   /**
    * @var integer
    */
    private $offset = 0;
    
   /**
    * @var string
    */
    private $subject;
    
   /**
    * @var integer
    */
    private $prefixLength = 0;

   /**
    * @var integer
    */
    private $placeholderLength = 0;

   /**
    * @var integer
    */
    private $lengthDifference = 0;
    
    public function __construct(Mailcode_Parser_Safeguard_Placeholder_Locator_Location $origin, string $replacementText, string $subject)
    {
        $this->replacementText = $replacementText;
        $this->origin = $origin;
        $this->placeholderText = $this->origin->getPlaceholder()->getReplacementText();
        $this->subject = $subject;
    }
    
    public function replace() : string
    {
        $this->prepareCalculations();
        $this->replaceLocation();
        $this->adjustPositions();
        
        return $this->subject;
    }
    
   /**
    * Adjusts the positions of all locations that come after 
    * this one, to account for the added string length of the
    * placeholder that has been replaced.
    */
    private function adjustPositions() : void
    {
        $locations = $this->origin->getNextAll();
        $offset = $this->lengthDifference;
        
        foreach($locations as $location)
        {
            $location->updatePositionByOffset($offset);
            
            $offset += $this->lengthDifference;
        }
    }
    
    private function prepareCalculations() : void
    {
        $length = mb_strpos($this->replacementText, $this->placeholderText);
        
        if($length === false)
        {
            throw new Mailcode_Exception(
                'Replacement text does not contain placeholder string.',
                sprintf(
                    'The placeholder string [%s] is not present in the replacement text: %s',
                    $this->placeholderText,
                    $this->replacementText
                ),
                self::ERROR_PLACEHOLDER_STRING_MISSING
            );
        }
        
        // Find the beginning position of the placeholder in the replacement text
        $this->prefixLength = $length;
        
        $this->placeholderLength = mb_strlen($this->placeholderText);
        
        // The total length that was added to the placeholder, front and back 
        $this->lengthDifference = mb_strlen($this->replacementText) - $this->placeholderLength;
    }
    
    private function replaceLocation() : void
    {
        // Get the starting position, with cumulated total offset
        $position = $this->origin->getStartPosition();
        
        // Cut the subject string so we can insert the adjusted placeholder
        $start = mb_substr($this->subject, 0, $position);
        $end = mb_substr($this->subject, $position + $this->placeholderLength);
        
        // Rebuild the subject string from the parts
        $this->subject = $start.$this->replacementText.$end;
        
        // Add the prefix length as offset to the location, now that 
        // we have added it. This way the position of the placeholder 
        // itself stays correct.
        $this->origin->updatePositionByOffset($this->prefixLength);
    }
}
