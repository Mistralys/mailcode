<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_NoHTML}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_NoHTML
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_NoHTML
 */
interface Mailcode_Interfaces_Commands_Validation_NoHTML
{
    public const VALIDATION_NAME_NOHTML = 'nohtml';

    public function setHTMLEnabled(bool $enabled=true) : self;

    public function isHTMLEnabled() : bool;

    public function getNoHTMLToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
