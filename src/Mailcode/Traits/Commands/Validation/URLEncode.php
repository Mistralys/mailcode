<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_URLEncode} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_URLEncode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a "urlencode:" keyword, to automatically set the
 * command's URL encoding flag.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 * @property Mailcode_Parser_Statement $params
 */
trait Mailcode_Traits_Commands_Validation_URLEncode
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected $urlencodeToken;

    protected function validateSyntax_urlencode() : void
    {
        $keywords = $this->params->getInfo()->getKeywords();

        foreach($keywords as $keyword)
        {
            if($keyword->isURLEncoded())
            {
                $this->urlencodeToken = $keyword;

                $this->setURLEncoding(true);

                break;
            }
        }
    }

    /**
     * @param bool $encoding
     * @return $this
     */
    abstract public function setURLEncoding(bool $encoding=true);

    public function getURLEncodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->urlencodeToken;
    }
}
