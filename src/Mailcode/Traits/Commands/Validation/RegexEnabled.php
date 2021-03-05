<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_RegexEnabled} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_RegexEnabled
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of the `regex:` keyword in the command statement,
 * and sets the regex enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 * @see Mailcode_Interfaces_Commands_RegexEnabled
 */
trait Mailcode_Traits_Commands_Validation_RegexEnabled
{
    /**
     * @var boolean
     */
    protected $regexEnabled = false;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected $regexToken;

    protected function validateSyntax_regex_enabled() : void
    {
        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_REGEX);

        $this->regexEnabled = $val->isValid();

        if($val->isValid())
        {
            $this->regexToken = $val->getToken();
        }
    }

    public function isRegexEnabled() : bool
    {
        return $this->regexEnabled;
    }

    public function getRegexToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if($this->regexToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $this->regexToken;
        }

        return null;
    }
}
