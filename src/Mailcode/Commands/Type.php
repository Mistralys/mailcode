<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Type} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Type
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Base command type interface. The opening, closing, etc. types all
 * extend this interface.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Commands_Command_Type
{
    public function getName() : string;
    
    public function getMatchedText() : string;
}
