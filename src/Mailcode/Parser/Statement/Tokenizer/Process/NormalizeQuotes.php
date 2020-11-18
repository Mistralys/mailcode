<?php

declare(strict_types=1);

namespace Mailcode;

/**
 * Some WYSIWYG editors like using pretty quotes instead
 * of the usual double quotes. This simply replaces all
 * occurrences with the regular variant.
 *
 */
class Mailcode_Parser_Statement_Tokenizer_Process_NormalizeQuotes extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $this->tokenized = str_replace(array('“', '”'), '"', $this->tokenized);
    }
}