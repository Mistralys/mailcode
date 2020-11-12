<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_CaseSensitive
{
    public function isCaseInsensitive() : bool;
    public function getCaseToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
