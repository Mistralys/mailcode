<?php
/**
 * File containing the {@see Mailcode_Renderer} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Renderer
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to easily create commands and echo them to standard output.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Printer extends Mailcode_Renderer
{
    protected function command2string(Mailcode_Commands_Command $command) : string
    {
        $string = parent::command2string($command);
        
        echo $string;
        
        return $string;
    }
}
