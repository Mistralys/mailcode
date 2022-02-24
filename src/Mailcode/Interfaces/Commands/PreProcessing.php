<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_PreProcessing}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Interfaces_Commands_PreProcessing
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for pre-processor commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Interfaces_Commands_PreProcessing
{
    public function preProcessOpening() : string;

    public function preProcessClosing() : string;
}
