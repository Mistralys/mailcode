<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_ListEndsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_ListEndsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF LIST ENDS WITH statement.
 *
 * Checks if a list variable value ends with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If_ListEndsWith extends Mailcode_Commands_Command_If_ListContains implements Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface
{
    use Mailcode_Traits_Commands_IfListEndsOrBeginsWith;

    public function getSearchPosition(): string
    {
        return self::SEARCH_POSITION_END;
    }
}
