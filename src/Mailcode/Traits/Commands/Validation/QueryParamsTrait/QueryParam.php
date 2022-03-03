<?php
/**
 * File containing the class {@see \Mailcode\Traits\Commands\Validation\QueryParamsTrait\QueryParam}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\QueryParamsTrait\QueryParam
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation\QueryParamsTrait;

use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

/**
 * Handles a single query parameter, by modifying the
 * matching tokenizer token directly. This ensures that
 * any changes are directly applied to the tokens.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
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
