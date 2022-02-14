<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Translator_Syntax_ApacheVelocity_Contains_StatementBuilder
{
    public const ERROR_INVALID_LIST_VARIABLE_NAME = 76701;

    /**
     * @var Mailcode_Variables_Variable
     */
    private $variable;

    /**
     * @var bool
     */
    private $caseSensitive;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
     */
    private $searchTerms;

    /**
     * @var string
     */
    private $containsType;

    /**
     * @var Mailcode_Translator_Syntax_ApacheVelocity
     */
    private $translator;

    /**
     * @var bool
     */
    private $regexEnabled;

    /**
     * Mailcode_Translator_Syntax_ApacheVelocity_Contains_StatementBuilder constructor.
     * @param Mailcode_Translator_Syntax_ApacheVelocity $translator
     * @param Mailcode_Variables_Variable $variable
     * @param bool $caseSensitive
     * @param bool $regexEnabled
     * @param Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[] $searchTerms
     * @param string $containsType
     */
    public function __construct(Mailcode_Translator_Syntax_ApacheVelocity $translator, Mailcode_Variables_Variable $variable, bool $caseSensitive, bool $regexEnabled, array $searchTerms, string $containsType)
    {
        $this->translator = $translator;
        $this->variable = $variable;
        $this->caseSensitive = $caseSensitive;
        $this->searchTerms = $searchTerms;
        $this->containsType = $containsType;
        $this->regexEnabled = $regexEnabled;
    }

    /**
     * Is this a not contains command? (list or regular)
     * @return bool
     */
    public function isNotContains() : bool
    {
        return strstr($this->containsType, 'not-contains') !== false;
    }

    /**
     * Is this a contains command to be used on a list variable?
     * @return bool
     */
    public function isList() : bool
    {
        return strstr($this->containsType, 'list-') !== false;
    }

    /**
     * Gets the sign to prepend the command with, i.e.
     * whether to add the negation "!" or not, depending
     * on the type of command.
     *
     * @return string
     */
    public function getSign() : string
    {
        if($this->isNotContains())
        {
            return '!';
        }

        return '';
    }

    /**
     * Gets the logical connector sign to combine several search
     * terms with, i.e. "&&" or "||" depending on whether it is
     * a regular contains or not contains.
     *
     * @return string
     */
    public function getConnector()
    {
        if($this->isNotContains())
        {
            return '&&';
        }

        return '||';
    }

    /**
     * @return string
     * @throws Mailcode_Exception
     */
    public function render() : string
    {
        $parts = array();

        foreach($this->searchTerms as $token)
        {
            $parts[] = $this->renderCommand($token);
        }

        return implode(' '.$this->getConnector().' ', $parts);
    }

    /**
     * @param Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm
     * @return string
     * @throws Mailcode_Exception
     */
    private function renderCommand(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm) : string
    {
        if($this->isList())
        {
            $command = $this->renderListCommand($searchTerm);
        }
        else
        {
            $command = $this->renderRegularCommand($searchTerm);
        }

        return $this->getSign().$command;
    }

    private function renderRegularCommand(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm) : string
    {
        return sprintf(
            '%s.matches(%s)',
            $this->variable->getFullName(),
            $this->renderRegex($searchTerm)
        );
    }

    /**
     * @param Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm
     * @return string
     * @throws Mailcode_Exception
     */
    private function renderListCommand(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm) : string
    {
        $name = $this->parseVarName();

        return sprintf(
            '$map.hasElement(%s.list(), "%s", %s)',
            '$'.$name['path'],
            $name['name'],
            $this->renderRegex($searchTerm)
        );
    }

    private function renderRegex(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $searchTerm) : string
    {
        $opts = 's';
        if($this->caseSensitive)
        {
            $opts = 'is';
        }

        $filtered = trim($searchTerm->getNormalized(), '"');

        if(!$this->regexEnabled)
        {
            $filtered = $this->translator->filterRegexString($filtered);
            $filtered = $this->addWildcards($filtered);
        }

        return sprintf(
            '"(?%s)%s"',
            $opts,
            $filtered
        );
    }

    /**
     * Adds the search wildcard before or after the search string
     * for the `list-begins-with` and `list-ends-with` command
     * flavors, or on both ends for the standard command.
     *
     * @param string $searchTerm
     * @return string
     */
    private function addWildcards(string $searchTerm) : string
    {
        if($this->containsType === 'list-begins-with')
        {
            return $searchTerm.'.*';
        }

        if($this->containsType === 'list-ends-with')
        {
            return '.*'.$searchTerm;
        }

        if($this->containsType === 'list-equals')
        {
            return '\\\A'.$searchTerm.'\\\Z';
        }

        return '.*'.$searchTerm.'.*';
    }

    /**
     * @return array<string,string>
     * @throws Mailcode_Exception
     */
     private function parseVarName() : array
     {
         $tokens = explode('.', ltrim($this->variable->getFullName(), '$'));

         if(count($tokens) === 2)
         {
             return array(
                 'path' => $tokens[0],
                 'name' => $tokens[1]
             );
         }

         throw new Mailcode_Exception(
             'Invalid variable name for a list property.',
             sprintf(
                 'Exactly 2 parts are required, variable [%s] has [%s].',
                 $this->variable->getFullName(),
                 count($tokens)
             ),
             self::ERROR_INVALID_LIST_VARIABLE_NAME
         );
     }
}

