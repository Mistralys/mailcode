<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Placeholder_Location} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Placeholder_Location
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Placeholder location container: stores information on
 * a placeholder's location within a subject string.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @see Mailcode_Parser_Safeguard_Placeholder::localizeInstances()
 */
class Mailcode_Parser_Safeguard_Placeholder_Locator_Location
{
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder
    */
    private $placeholder;
    
   /**
    * @var int
    */
    private $startPos;
    
   /**
    * @var int
    */
    private $length;
    
   /**
    * @var int
    */
    private $index;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder_Locator
    */
    private $locator;
    
    public function __construct(Mailcode_Parser_Safeguard_Placeholder_Locator $locator, Mailcode_Parser_Safeguard_Placeholder $placeholder, int $index, int $startPos, int $length)
    {
        $this->placeholder = $placeholder;
        $this->index = $index;
        $this->startPos = $startPos;
        $this->length = $length;
        $this->locator = $locator;
    }
    
    public function getID() : string
    {
        return 'PH'.$this->placeholder->getID().'-IDX'.$this->index.'@'.$this->startPos;
    }
    
    public function getIndex() : int
    {
        return $this->index;
    }
    
    public function getPlaceholderString() : string
    {
        return $this->placeholder->getReplacementText();
    }
    
    public function getStartPosition() : int
    {
        return $this->startPos;
    }
    
    public function getEndPosition() : int
    {
        return $this->getStartPosition() + $this->getLength();
    }
    
    public function getLength() : int
    {
        return $this->length;
    }
    
    public function getSubjectString() : string
    {
        return $this->locator->getSubjectString();
    }
    
    public function getSubjectLength() : int
    {
        return mb_strlen($this->getSubjectString());
    }
    
    public function getPlaceholder() : Mailcode_Parser_Safeguard_Placeholder
    {
        return $this->placeholder;
    }
    
    public function hasNext() : bool
    {
        return $this->getNext() !== null;
    }
    
    public function hasPrevious() : bool
    {
        return $this->getPrevious() !== null;
    }
    
   /**
    * Retrieves the placeholder location right after this one, if any.
    * 
    * @return Mailcode_Parser_Safeguard_Placeholder_Locator_Location|NULL
    */
    public function getNext() : ?Mailcode_Parser_Safeguard_Placeholder_Locator_Location
    {
        return $this->locator->getLocationByIndex(($this->index-1));
    }
    
   /**
    * Retrieves the placeholder location right before this one, if any.
    * 
    * @return Mailcode_Parser_Safeguard_Placeholder_Locator_Location|NULL
    */
    public function getPrevious() : ?Mailcode_Parser_Safeguard_Placeholder_Locator_Location
    {
        return $this->locator->getLocationByIndex(($this->index+1));
    }
    
   /**
    * Retrieves all placeholder locations that come after this one, if any.
    * 
    * @return Mailcode_Parser_Safeguard_Placeholder_Locator_Location[]
    */
    public function getNextAll() : array
    {
        $locations = $this->locator->getLocations();
        
        return array_slice($locations, $this->index+1);
    }
    
    public function updatePositionByOffset(int $offset) : void
    {
        $this->startPos += $offset;
    }
}
