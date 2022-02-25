<?php

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_QueryParams
 */
interface Mailcode_Interfaces_Commands_Validation_QueryParams
{
    public const VALIDATION_NAME_QUERY_PARAMS = 'query_params';

    public function hasQueryParams() : bool;

    /**
     * @return array<string,string>
     */
    public function getQueryParams() : array;

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    public function getQueryParamTokens() : array;

    public function getQueryParam(string $name) : string;

    public function hasQueryParam(string $name) : bool;
}
