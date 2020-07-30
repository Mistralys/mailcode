<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Placeholder_Locator} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Placeholder_Locator
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Utility that can localize all instances of a command placeholder in 
 * a string, with the possibility to transform the placeholders at will.
 * 
 * It is used by the safeguard formatters to modify the placeholders
 * when making a safeguarded string whole again. 
 * 
 * For example, the SingleLines formatter will add the newlines in front 
 * and back of logic commands to ensure they are all on a single line.
 * The locator allows doing this while shifting the placeholders around
 * with the added string lengths.  
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Placeholder_Locator
{
   /**
    * @var string
    */
    private $subject;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder
    */
    private $placeholder;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder_Locator_Location[]
    */
    private $instances = array();
    
    public function __construct(Mailcode_Parser_Safeguard_Placeholder $placeholder, string $subject)
    {
        $this->placeholder = $placeholder;
        $this->subject = $subject;
        
        $this->localizeInstances();
    }
    
   /**
    * Attempts to find the placeholder's instances in the
    * target string, and returns a location instance for
    * each, which allows accessing their exact position.
    */
    private function localizeInstances() : void
    {
        $lastPos = 0;
        $needle = $this->placeholder->getReplacementText();
        $index = 0;
        
        while(($lastPos = mb_strpos($this->subject, $needle, $lastPos)) !== false)
        {
            $length = mb_strlen($needle);
            
            $this->instances[] = new Mailcode_Parser_Safeguard_Placeholder_Locator_Location(
                $this,
                $this->placeholder,
                $index,
                $lastPos,
                $length
            );
            
            $lastPos = $lastPos + mb_strlen($needle);
            
            $index++;
        }
    }
    
   /**
    * @return Mailcode_Parser_Safeguard_Placeholder_Locator_Location[]
    */
    public function getLocations() : array
    {
        return $this->instances;
    }
    
   /**
    * Retrieves a location by its index in the locations list (in 
    * ascending order as found in the subject string, zero based.)
    * 
    * @param int $index
    * @return Mailcode_Parser_Safeguard_Placeholder_Locator_Location|NULL
    */
    public function getLocationByIndex(int $index) : ?Mailcode_Parser_Safeguard_Placeholder_Locator_Location
    {
        if(isset($this->instances[$index]))
        {
            return $this->instances[$index];
        }
        
        return null;
    }
    
   /**
    * Replaces the placeholder at the location with the specified
    * replacement text.
    * 
    * NOTE: The replacement text MUST contain the original placeholder
    * for this to work. An exception will be triggered otherwise.
    * 
    * @param Mailcode_Parser_Safeguard_Placeholder_Locator_Location $location
    * @param string $replacementText
    * @throws Mailcode_Exception
    * 
    * @see Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer::ERROR_PLACEHOLDER_STRING_MISSING
    */
    public function replaceWith(Mailcode_Parser_Safeguard_Placeholder_Locator_Location $location, string $replacementText) : void
    {
        $replacer = new Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer(
            $location, 
            $replacementText,
            $this->subject
        );
        
        $this->subject = $replacer->replace();
    }
    
   /**
    * Retrieves the subject string, with all changes that were made, if any. 
    *  
    * @return string
    */
    public function getSubjectString() : string
    {
        return $this->subject;
    }
}
