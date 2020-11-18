<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_ProtectedContent extends Mailcode_Commands_Command_Type_Opening
{
    const ERROR_INVALID_NESTING_NO_END = 73201;

    public function getContent() : string;
    public function getContentPlaceholder() : string;
    public function protectContent(string $string, Mailcode_Parser_Safeguard_Placeholder $open, Mailcode_Parser_Safeguard_Placeholder $end) : string;
    public function restoreContent(string $string) : string;
}
