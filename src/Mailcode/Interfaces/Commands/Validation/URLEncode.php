<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_URLEncode
{
    const VALIDATION_NAME_URLENCODE = 'urlencode';

    public function getURLEncodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
