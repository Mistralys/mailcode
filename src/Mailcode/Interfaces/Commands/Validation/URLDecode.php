<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Validation_URLDecode
{
    public const VALIDATION_NAME_URLDECODE = 'urldecode';

    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    /**
     * @param bool $decode
     * @return $this
     */
    public function setURLDecoding(bool $decode=true);
}
