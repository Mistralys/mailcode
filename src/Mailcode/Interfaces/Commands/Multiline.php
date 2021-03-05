<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Multiline
{
    public function isMultiline() : bool;
    public function getMultilineToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
