<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_StringLiterals extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $matches = array();
        preg_match_all('/"(.*)"/sxU', $this->tokenized, $matches, PREG_PATTERN_ORDER);

        foreach($matches[0] as $match)
        {
            $this->registerToken('StringLiteral', $match);
        }
    }
}