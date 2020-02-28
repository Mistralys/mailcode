<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory
{
    public static function createShowVariable(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        $variableName = '$'.ltrim($variableName, '$');
        
        return new Mailcode_Commands_Command_ShowVariable(
            '', 
            $variableName,
            '{showvar:'.$variableName.'}'
        );
    }
}
