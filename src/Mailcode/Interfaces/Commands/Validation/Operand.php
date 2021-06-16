<?php

declare(strict_types=1);

namespace Mailcode;

interface Mailcode_Interfaces_Commands_Operand
{
    const VALIDATION_NAME = 'operand';

    public function getOperand() : Mailcode_Parser_Statement_Tokenizer_Token_Operand;
    public function getSign() : string;
}
