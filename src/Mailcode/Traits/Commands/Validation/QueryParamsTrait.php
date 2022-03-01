<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\QueryParamsTrait} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\QueryParamsTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\QueryParamsInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

/**
 * Command validation drop-in: checks for the presence
 * of query parameters specified in separate string
 * literals, identified by the format <code>"paramName=paramValue"</code>.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see QueryParamsInterface
 */
trait QueryParamsTrait
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

    public function setQueryParam(string $name, string $value) : self
    {
        $this->queryParams[$name] = $value;
        return $this;
    }

    public function removeQueryParam(string $name) : self
    {
        if(isset($this->queryParams[$name]))
        {
            unset($this->queryParams[$name]);
        }

        return $this;
    }
}
