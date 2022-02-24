<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
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
class Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting extends Mailcode_Parser_Safeguard_Formatter_ReplacerType
{
    use Mailcode_Traits_Formatting_HTMLHighlighting;
    
    protected function initFormatting() : void
    {
        // nothing to do here
    }
}
