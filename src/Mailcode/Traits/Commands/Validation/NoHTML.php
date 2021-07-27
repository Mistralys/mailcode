<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_NoHTML} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_NoHTML
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
 *
 * @see Mailcode_Interfaces_Commands_Validation_NoHTML
 */
trait Mailcode_Traits_Commands_Validation_NoHTML
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected $noHTMLToken = null;

    protected function validateSyntax_nohtml() : void
    {
        $this->noHTMLToken = null;

        $keywords = $this->params->getInfo()->getKeywords();

        foreach($keywords as $keyword)
        {
            if($keyword->isNoHTML())
            {
                $this->noHTMLToken = $keyword;
                break;
            }
        }
    }

    /**
     * Sets the HTML generation mode for the command.
     *
     * @param bool $enabled
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setHTMLEnabled(bool $enabled=true)
    {
        $this->params->getInfo()->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_NOHTML, !$enabled);

        $this->validateSyntax_nohtml();

        return $this;
    }

    /**
     * Whether generating HTML code is enabled for the command.
     * @return bool
     */
    public function isHTMLEnabled() : bool
    {
        return !isset($this->noHTMLToken);
    }

    public function getNoHTMLToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->noHTMLToken;
    }
}
