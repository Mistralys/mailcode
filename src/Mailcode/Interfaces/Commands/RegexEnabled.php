<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_RegexEnabled
{
    public function isRegexEnabled() : bool;
    public function getRegexToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
