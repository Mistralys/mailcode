<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * PreProcessor formatter: replaces commands that support being
 * pre processed with their corresponding generated output.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing extends Mailcode_Parser_Safeguard_Formatter_Type_Normalized
{
    public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location): string
    {
        $command = $location->getPlaceholder()->getCommand();

        if($command instanceof Mailcode_Interfaces_Commands_PreProcessing)
        {
            return $command->preProcessOpening();
        }

        if($command instanceof Mailcode_Commands_Command_Type_Closing)
        {
            $opening = $command->getOpeningCommand();

            if($opening instanceof Mailcode_Interfaces_Commands_PreProcessing) {
                return $opening->preProcessClosing();
            }
        }

        return parent::getReplaceString($location);
    }
}
