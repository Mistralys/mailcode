<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Type_Sibling} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Type_Sibling
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Base structure for opening commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Commands_Command_Type_Sibling
{
    public function getParentName() : string;
}
