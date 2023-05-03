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

use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use function Mailcode\t;

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
    private bool $breakAtEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token $token = null;

    protected function validateSyntax_break_at(): void
    {
        $this->token = $this->requireParams()->getInfo()->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_BREAK_AT);

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_BREAK_AT);

        $this->breakAtEnabled = $val->isValid() && $this->token != null;;

        if ($this->breakAtEnabled) {
            if (!$this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number &&
                !$this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
                $this->validationResult->makeError(
                    t('Invalid break-at usage'),
                    Mailcode_Commands_Command_For::VALIDATION_BREAK_AT_WRONG_PARAMETER
                );
                return;
            }
        }
    }

    public function isBreakAtEnabled(): bool
    {
        return $this->breakAtEnabled;
    }

    public function getBreakAtToken(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->token;
    }

}
