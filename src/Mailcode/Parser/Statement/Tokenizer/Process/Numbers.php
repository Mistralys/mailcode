<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_Numbers extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $matches = array();
        preg_match_all('/-*[0-9]+\s*[.,]\s*[0-9]+|-*[0-9]+/sx', $this->tokenized, $matches, PREG_PATTERN_ORDER);

        foreach($matches[0] as $match)
        {
            $this->registerToken('Number', $match);
        }
    }
}