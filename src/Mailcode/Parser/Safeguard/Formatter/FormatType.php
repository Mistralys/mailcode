<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Highlighting} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Highlighting
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for replacer formatters that replace the placeholders.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Safeguard_Formatter_FormatType extends Mailcode_Parser_Safeguard_Formatter
{
    public function getPriority() : int
    {
        return PHP_INT_MAX;
    }

   /**
    * Formats the specified string according to the formatter.
    * Retrieve the updated string from the string container used
    * to create the formatter, or use `getSubjectString()`.
    */
    public function format() : void
    {
        $locations = $this->resolveLocations();
        
        foreach($locations as $location)
        {
            $location->format();
            
            $this->log = array_merge($this->log, $location->getLog());
        }
    }
}
