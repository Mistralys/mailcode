<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Traits_Commands_Validation_URLDecode} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Traits_Commands_Validation_URLDecode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a "urldecode:" keyword.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 * @property Mailcode_Parser_Statement $params
 *
 * @see Mailcode_Interfaces_Commands_URLDecode
 */
trait Mailcode_Traits_Commands_Validation_URLDecode
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected $urldecodeToken;

    protected function validateSyntax_urldecode() : void
    {
        $keywords = $this->params->getInfo()->getKeywords();

        foreach($keywords as $keyword)
        {
            if($keyword->isURLDecode())
            {
                $this->urldecodeToken = $keyword;
                break;
            }
        }
    }

    abstract public function setURLDecoding(bool $decode=true);

    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->urldecodeToken;
    }
}
