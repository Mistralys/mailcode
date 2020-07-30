<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_SingleLines} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_SingleLines
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
abstract class Mailcode_Parser_Safeguard_FormatterLocation
{
   /**
    * @var Mailcode_Parser_Safeguard_Formatter
    */
    protected $formatter;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder_Locator_Location
    */
    protected $location;
    
    public function __construct(Mailcode_Parser_Safeguard_Formatter $formatter, Mailcode_Parser_Safeguard_Placeholder_Locator_Location $location)
    {
        $this->formatter = $formatter;
        $this->location = $location;
        
        $this->init();
    }
    
    abstract protected function init() : void; 
    
   /**
    * Whether this specific placeholder location needs to be adjusted.
    * 
    * @return bool
    */
    abstract public function requiresAdjustment() : bool;
    
    abstract protected function getAdjustedText() : string;
    
   /**
    * Retrieves the placeholder text, adjusted as needed by the
    * formatter. If no adjustments are required, this will simply
    * return the vanilla placeholder string.
    *  
    * @return string
    */
    public function getPlaceholderText() : string
    {
        if($this->requiresAdjustment())
        {
            $text = $this->getAdjustedText();
            
            return $text;
        }
        
        return $this->location->getPlaceholderString();
    }
}
