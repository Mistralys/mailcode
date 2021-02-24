<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf_ListBeginsWith} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf_ListBeginsWith
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening ELSE IF LIST BEGINS WITH statement.
 *
 * Checks if a list variable value begins with a search string.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf_ListBeginsWith extends Mailcode_Commands_Command_ElseIf_ListContains implements Mailcode_Traits_Commands_IfListEndsOrBeginsWithInterface
{
    use Mailcode_Traits_Commands_IfListEndsOrBeginsWith;

    public function getSearchPosition(): string
    {
        return self::SEARCH_POSITION_BEGINNING;
    }
}
