<?php

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_NoTracking
 */
interface Mailcode_Interfaces_Commands_Validation_NoTracking
{
    public const VALIDATION_NAME_NO_TRACKING = 'no_tracking';

    public function isTrackingEnabled() : bool;
    public function getNoTrackingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
