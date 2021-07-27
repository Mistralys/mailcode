<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_NoHTML
{
    const VALIDATION_NAME_NOHTML = 'nohtml';

    public function setHTMLEnabled(bool $enabled=true);

    public function isHTMLEnabled() : bool;

    public function getNoHTMLToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
