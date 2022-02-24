<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Placeholders} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Placeholders
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Placeholders formatter: replaces commands by placeholders to 
 * safeguard them. Used by the Safeguard's makeSafe method.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_Placeholders extends Mailcode_Parser_Safeguard_Formatter_ReplacerType
{
    protected function initFormatting() : void
    {
    }
    
    public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location): string
    {
        return $location->getPlaceholder()->getReplacementText();
    }
}
