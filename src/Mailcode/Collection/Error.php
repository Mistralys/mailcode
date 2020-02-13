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
abstract class Mailcode_Collection_Error
{
    protected $code = 0;
    
    protected $matchedText = '';
    
    protected $message = '';
    
    public function getCode() : int
    {
        return $this->code;
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }
    
    public function getMessage() : string
    {
        return $this->message;
    }
    
    public function isUnknownCommand() : bool
    {
        return $this->code === Mailcode_Commands_Command::VALIDATION_UNKNOWN_COMMAND_NAME;
    }
    
    public function isTypeNotSupported() : bool
    {
        return $this->code === Mailcode_Commands_Command::VALIDATION_ADDONS_NOT_SUPPORTED;
    }
}
