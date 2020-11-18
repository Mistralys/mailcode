<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_Keywords extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $keywords = Mailcode_Commands_Keywords::getAll();

        foreach($keywords as $keyword)
        {
            if(strstr($this->tokenized, $keyword))
            {
                $this->registerToken('Keyword', $keyword);
            }
        }
    }
}