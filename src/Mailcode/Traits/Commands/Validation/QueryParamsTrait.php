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

use Mailcode\Commands\ParamsException;
use Mailcode\Interfaces\Commands\Validation\QueryParamsInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Traits\Commands\Validation\QueryParamsTrait\QueryParam;

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
     * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    private array $queryParamTokens = array();

    protected function validateSyntax_query_params() : void
    {

    }

    public function hasQueryParams() : bool
    {
        $params = $this->collectParams();
        return !empty($params);
    }

    /**
     * @return array<string,string>
     */
    public function getQueryParams() : array
    {
        $params = $this->collectParams();
        $result = array();

        foreach($params as $param)
        {
            $result[$param->getName()] = $param->getValue();
        }

        return $result;
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    public function getQueryParamTokens() : array
    {
        $params = $this->collectParams();
        $result = array();

        foreach($params as $param)
        {
            $result[] = $param->getToken();
        }

        return $result;
    }

    /**
     * @return array<string,QueryParam>
     * @throws ParamsException
     */
    private function collectParams() : array
    {
        $literals = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        $result = array();

        foreach($literals as $literal)
        {
            $text = $literal->getText();

            if(strpos($text, '=') !== false)
            {
                $param = new QueryParam($literal);
                $result[$param->getName()] = $param;
            }
        }

        return $result;
    }

    public function getQueryParam(string $name) : string
    {
        $params = $this->collectParams();

        if(isset($params[$name]))
        {
            return $params[$name]->getValue();
        }

        return '';
    }

    public function hasQueryParam(string $name) : bool
    {
        $params = $this->collectParams();

        return isset($params[$name]);
    }

    public function setQueryParam(string $name, string $value) : self
    {
        $params = $this->collectParams();

        if(isset($params[$name]))
        {
            $params[$name]->setValue($value);
        }
        else
        {
            $this->addParam($name, $value);
        }

        return $this;
    }

    private function addParam(string $name, string $value) : void
    {
        $this->requireParams()
            ->getInfo()
            ->addStringLiteral(sprintf('%s=%s', $name, $value));
    }

    public function removeQueryParam(string $name) : self
    {
        $params = $this->collectParams();

        if(isset($params[$name]))
        {
            $this->requireParams()
                ->getInfo()
                ->removeToken($params[$name]->getToken());
        }

        return $this;
    }
}
