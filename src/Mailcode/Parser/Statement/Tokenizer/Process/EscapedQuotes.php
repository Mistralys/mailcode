<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_EscapedQuotes extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $this->tokenized = str_replace('\"', '__QUOTE__', $this->tokenized);
    }
}