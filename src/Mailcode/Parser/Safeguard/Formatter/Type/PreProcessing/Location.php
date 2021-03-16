<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Normalized_Location} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Normalized_Location
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
 * @property Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing_Location extends Mailcode_Parser_Safeguard_Formatter_Type_Normalized_Location
{
    public function requiresAdjustment(): bool
    {
        $command = $this->getPlaceholder()->getCommand();

        return
            $command instanceof Mailcode_Interfaces_Commands_PreProcessing
                ||
            ($command instanceof Mailcode_Commands_Command_Type_Closing && $command->getOpeningCommand() instanceof Mailcode_Interfaces_Commands_PreProcessing);
    }
}
