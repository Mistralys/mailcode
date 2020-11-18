<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

class Mailcode_Parser_Statement_Tokenizer_Process_ExtractTokens extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        // split the string by the delimiters: this gives an
        // array with tokenIDs, and any content that may be left
        // over that could not be tokenized.
        $parts = ConvertHelper::explodeTrim($this->delimiter, $this->tokenized);
        $tokens = array();

        foreach($parts as $part)
        {
            $token = $this->getTokenByID($part);

            // if the entry is a token, simply add it.
            if($token)
            {
                $tokens[] = $token;
            }
            // anything else is added as an unknown token.
            else
            {
                $tokens[] = $this->createToken('Unknown', $part);
            }
        }

        $this->tokensTemporary = $tokens;
    }
}
