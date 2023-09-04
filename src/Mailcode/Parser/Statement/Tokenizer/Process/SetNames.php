<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_SetNames extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        foreach ($this->tokensTemporary as $i => $token)
        {
            $next = $this->tokensTemporary[$i + 1] ?? null;

            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_ParamName && $next) {
                $next->setName($token->getName());
            }
        }
    }
}