<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\ShortenTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\ShortenTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\ShortenInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Command validation drop-in: checks for the presence
 * of the `shorten:` keyword in the command statement,
 * and sets the shorten enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Daniel Storch <daniel.storch@ionos.com>
 *
 * @see ShortenInterface
 */
trait ShortenTrait
{
    public function isShortenEnabled() : bool
    {
        return $this->requireParams()
            ->getInfo()
            ->hasKeyword(Mailcode_Commands_Keywords::TYPE_SHORTEN);
    }

    public function setShortenEnabled(bool $enabled) : self
    {
        $this->requireParams()
            ->getInfo()
            ->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_SHORTEN, $enabled);

        return $this;
    }

    public function getShortenToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->requireParams()
            ->getInfo()
            ->getKeywordsCollection()
            ->getByName(Mailcode_Commands_Keywords::TYPE_SHORTEN);
    }

    protected function validateSyntax_shorten() : void
    {
    }
} 