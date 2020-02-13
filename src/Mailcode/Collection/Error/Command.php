<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Collection
 * @see Mailcode_Commands_Command_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a variable value.
 *
 * @package Mailcode
 * @subpackage Collection
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Collection_Error_Command extends Mailcode_Collection_Error
{
    public function __construct(Mailcode_Commands_Command $command)
    {
        $result = $command->getValidationResult();
        
        $this->matchedText = $command->getMatchedText();
        $this->code = $result->getCode();
        $this->message = t('Error in command %1$s:', $command->getName()).' '.$result->getErrorMessage();
    }
}
