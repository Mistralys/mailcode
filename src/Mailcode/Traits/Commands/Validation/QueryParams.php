<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_QueryParams} trait.
 *
 * @see Mailcode_Traits_Commands_Validation_QueryParams
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of query parameters specified in separate string
 * literals, identified by the format <code>"paramName=paramValue"</code>.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Interfaces_Commands_Validation_QueryParams
 */
trait Mailcode_Traits_Commands_Validation_QueryParams
{
    /**
     * @var array<string,string>
     */
    private array $queryParams = array();

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    private array $queryParamTokens = array();

    protected function validateSyntax_query_params() : void
    {
        $literals = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        foreach($literals as $literal)
        {
            $text = $literal->getText();

            if(strpos($text, '=') !== false && $this->analyzeQueryString($text))
            {
                $this->queryParamTokens[] = $literal;
            }
        }
    }

    private function analyzeQueryString(string $param) : bool
    {
        $tokens = explode('=', $param);
        $name = trim(array_shift($tokens));
        $value = trim(implode('=', $tokens));

        $this->queryParams[$name] = $value;

        return true;
    }

    public function hasQueryParams() : bool
    {
        return !empty($this->queryParams);
    }

    /**
     * @return array<string,string>
     */
    public function getQueryParams() : array
    {
        return $this->queryParams;
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    public function getQueryParamTokens() : array
    {
        return $this->queryParamTokens;
    }

    public function getQueryParam(string $name) : string
    {
        if(isset($this->queryParams[$name]))
        {
            return $this->queryParams[$name];
        }

        return '';
    }

    public function hasQueryParam(string $name) : bool
    {
        return isset($this->queryParams[$name]);
    }
}
