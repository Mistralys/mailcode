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
   /**
    * @var integer
    */
    protected int $code = 0;

   /**
    * @var string
    */
    protected string $matchedText = '';

   /**
    * @var string
    */
    protected string $message = '';

    protected ?Mailcode_Commands_Command $command = null;
    
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

    public function getCommand() : ?Mailcode_Commands_Command
    {
        return $this->command;
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
