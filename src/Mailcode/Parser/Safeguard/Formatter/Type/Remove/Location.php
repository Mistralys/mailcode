<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Remove_Location} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Remove_Location
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * These locations never need any adjustment.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Safeguard_Formatter_Type_Remove $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_Type_Remove_Location extends Mailcode_Parser_Safeguard_Formatter_Location
{
    protected function init() : void
    {
    }
    
    public function requiresAdjustment() : bool
    {
        return true;
    }
}
