<?php

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation\QueryParamsTrait;

use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

class QueryParam
{
    private Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $token;

    public function __construct(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $token)
    {
        $this->token = $token;
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
     */
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->token;
    }

    public function getName() : string
    {
        $split = $this->splitString();

        return $split['name'];
    }

    public function getValue() : string
    {
        $split = $this->splitString();

        return $split['value'];
    }

    public function setValue(string $value) : self
    {
        $this->token->setText($this->getName().'='.$value);
        return $this;
    }

    /**
     * @return array{name:string,value:string}
     */
    private function splitString() : array
    {
        $tokens = explode('=', $this->token->getText());

        return array(
            'name' => trim(array_shift($tokens)),
            'value' => trim(implode('=', $tokens))
        );
    }
}
