<?php
/**
 * @package Mailcode
 * @subpackage Validation
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\AbsoluteKeywordInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Command validation drop-in: checks for the presence
 * of the `absolute:` keyword in the command statement,
 * and sets the absolute number flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see AbsoluteKeywordInterface
 */
trait AbsoluteKeywordTrait
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    private ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword $absoluteKeyword = null;

    protected function validateSyntax_absolute(): void
    {
        $keywords = $this->requireParams()
            ->getInfo()
            ->getKeywords();

        foreach ($keywords as $keyword) {
            if ($keyword->getKeyword() === Mailcode_Commands_Keywords::TYPE_ABSOLUTE) {
                $this->absoluteKeyword = $keyword;
                break;
            }
        }
    }

    public function isAbsolute(): bool
    {
        return isset($this->absoluteKeyword);
    }

    /**
     * @inheritdoc
     * @return $this
     */
    public function setAbsolute(bool $absolute): self
    {
        if ($absolute === false && isset($this->absoluteKeyword)) {
            $this->requireParams()->getInfo()->removeKeyword($this->absoluteKeyword->getKeyword());
            $this->absoluteKeyword = null;
        }

        if ($absolute === true && !isset($this->absoluteKeyword)) {
            $this->requireParams()
                ->getInfo()
                ->addKeyword(Mailcode_Commands_Keywords::TYPE_ABSOLUTE);

            $this->validateSyntax_absolute();
        }

        return $this;
    }
}
