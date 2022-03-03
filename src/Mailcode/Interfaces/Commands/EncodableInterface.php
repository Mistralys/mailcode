<?php

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

interface EncodableInterface extends Mailcode_Interfaces_Commands_Command
{
    public const ERROR_UNSUPPORTED_ENCODING = 102001;

    /**
     * @return string[]
     */
    public function getSupportedEncodings() : array;

    public function supportsEncoding(string $keyword) : bool;

    public function isEncodingEnabled(string $keyword) : bool;

    /**
     * @param string $keyword
     * @param bool $enabled
     * @return $this
     */
    public function setEncodingEnabled(string $keyword, bool $enabled) : self;

    /**
     * Retrieves the names of the currently enabled
     * encodings (keywords), in the exact order they
     * were specified in the command's parameters.
     *
     * @return string[]
     */
    public function getActiveEncodings() : array;

    public function hasActiveEncodings() : bool;

    public function getEncodingToken(string $keyword) : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
