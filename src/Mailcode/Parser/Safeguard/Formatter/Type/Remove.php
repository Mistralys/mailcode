<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Remove} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Remove
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Default formatter: Only replaces commands with their normalized strings.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_Remove extends Mailcode_Parser_Safeguard_Formatter_ReplacerType
{
    protected function initFormatting() : void
    {
    }

    public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location): string
    {
        return '';
    }
}
