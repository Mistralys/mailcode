<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_Operands extends Mailcode_Parser_Statement_Tokenizer_Process
{
    /**
     * @var string[]
     */
    private $operands = array(
        '==',
        '<=',
        '>=',
        '!=',
        '=',
        '+',
        '-',
        '/',
        '*',
        '>',
        '<'
    );

    protected function _process() : void
    {
        foreach($this->operands as $operand)
        {
            if(strstr($this->tokenized, $operand))
            {
                $this->registerToken('Operand', $operand);
            }
        }
    }
}
