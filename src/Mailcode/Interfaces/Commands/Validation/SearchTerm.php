<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_SearchTerm}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_SearchTerm
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_SearchTerm
 */
interface Mailcode_Interfaces_Commands_Validation_SearchTerm
{
    public const VALIDATION_NAME_SEARCH_TERM = 'search_term';

    public function getSearchTerm() : string;
}
