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
class Mailcode_Collection_Error_Message extends Mailcode_Collection_Error
{
    public function __construct(string $matchedText, int $code, string $message)
    {
        $this->matchedText = $matchedText;
        $this->code = $code;
        $this->message = $message;
    }
}
