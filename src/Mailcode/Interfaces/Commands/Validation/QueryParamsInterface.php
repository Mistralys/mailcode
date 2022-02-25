<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\QueryParamsInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\QueryParamsInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Traits\Commands\Validation\QueryParamsTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see QueryParamsTrait
 */
interface QueryParamsInterface
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
