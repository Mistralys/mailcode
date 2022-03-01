<?php

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

class Mailcode_Parser_Statement_Tokenizer_Process_EncodeSpecialChars extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $this->tokenized = SpecialChars::encodeEscaped($this->tokenized);
    }
}
