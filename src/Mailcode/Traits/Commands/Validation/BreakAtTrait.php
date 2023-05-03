<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\NoTrackingTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\NoTrackingTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;

/**
 * Command validation drop-in: checks for the presence
 * of the `break-at:` keyword in the command statement.
 *
 * @package Mailcode
 * @subpackage Validation
 *
 * @see BreakAtInterface
 */
trait BreakAtTrait
{
    /**
     * @var boolean
     */
    protected bool $breakAtEnabled = false;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword $breakAtToken;

    protected function validateSyntax_break_at(): void
    {
        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_BREAK_AT);

        $this->breakAtEnabled = $val->isValid();

        if ($val->isValid()) {
            $this->breakAtToken = $val->getToken();
        }
    }

    public function isBreakAtEnabled(): bool
    {
        return $this->breakAtEnabled;
    }

    public function getBreakAtToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if ($this->breakAtToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword) {
            return $this->breakAtToken;
        }

        return null;
    }
}
