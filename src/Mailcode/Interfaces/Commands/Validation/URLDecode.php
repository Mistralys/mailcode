<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_URLDecode
{
    const VALIDATION_NAME = 'urldecode';

    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    /**
     * @param bool $decode
     * @return $this
     */
    public function setURLDecoding(bool $decode=true);
}
