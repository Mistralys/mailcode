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

use AppUtils\ConvertHelper;

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
    const ERROR_PLACEHOLDER_POSITION_ERRONEOUS = 63503;
    
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
    private $placeholderOffset = 0;

   /**
    * @var integer
    */
    private $placeholderLength = 0;

   /**
    * @var integer
    */
    private $lengthDifference = 0;
    
    public function __construct(Mailcode_Parser_Safeguard_Placeholder_Locator $locator, Mailcode_Parser_Safeguard_Placeholder_Locator_Location $origin, string $replacementText, string $subject)
    {
        $this->locator = $locator;
        $this->replacementText = $replacementText;
        $this->origin = $origin;
        $this->placeholderText = $this->origin->getPlaceholder()->getReplacementText();
        $this->subject = $subject;
    }
    
    public function replace() : void
    {
        $this->prepareCalculations();
        $this->replaceLocation();
        $this->adjustPositions();
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
        $this->placeholderOffset = $length;
        
        $this->placeholderLength = mb_strlen($this->placeholderText);
        
        // The total length that was added to the placeholder, front and back 
        $this->lengthDifference = mb_strlen($this->replacementText) - $this->placeholderLength;
    }
    
    private function replaceLocation() : void
    {
        // Get the starting position, with cumulated total offset
        $startPosition = $this->origin->getStartPosition();
        $endPosition = $startPosition + $this->placeholderLength;
        
        // Cut the subject string so we can insert the adjusted placeholder
        $start = mb_substr($this->subject, 0, $startPosition);
        $end = mb_substr($this->subject, $endPosition);
        $placeholder = mb_substr($this->subject, $startPosition, $this->placeholderLength);

        // Failsafe check: the calculated new position
        // in the subject string should still equal the
        // placeholder string.
        if($placeholder != $this->placeholderText)
        {
            throw new Mailcode_Exception(
                'Localizing a safeguard placeholder failed.',
                sprintf(
                    'The placeholder [%s] was not found at the position [%s] in the subject string. Instead, found [%s].',
                    ConvertHelper::hidden2visible($this->placeholderText),
                    $startPosition,
                    ConvertHelper::hidden2visible($placeholder)
                ),
                self::ERROR_PLACEHOLDER_POSITION_ERRONEOUS
            );
        }
        
        // Rebuild the subject string from the parts
        $this->subject = $start.$this->replacementText.$end;
        
        $this->locator->handle_subjectModified($this->subject, $this);
        
        // Add the amount of characters that the placeholder
        // has been moved to the left to this location.
        $this->origin->updatePositionByOffset($this->placeholderOffset);
    }
}
