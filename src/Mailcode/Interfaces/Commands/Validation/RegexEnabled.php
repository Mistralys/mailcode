<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_RegexEnabled
{
    public const VALIDATION_NAME_REGEX_ENABLED = 'regex_enabled';

    public function isRegexEnabled() : bool;
    public function getRegexToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
