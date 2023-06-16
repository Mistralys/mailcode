<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\BreakAtTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\BreakAtTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\BreakAtInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_StringLiterals;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
use function Mailcode\t;

/**
 * Command validation drop-in: checks for the presence
 * of the `break-at:` keyword in the command statement.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see BreakAtInterface
 */
trait BreakAtTrait
{
    private bool $breakAtEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token $breakAtToken = null;

    protected function validateSyntax_check_break_at(): void
    {
        $this->breakAtToken = $this->requireParams()->getInfo()->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_BREAK_AT);

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_BREAK_AT);

        $this->breakAtEnabled = $val->isValid() && $this->breakAtToken != null;;

        if ($this->breakAtEnabled) {
            if (!$this->breakAtToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number &&
                !$this->breakAtToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
                $this->validationResult->makeError(
                    t('Invalid break-at type.' . ' ' . 'Expected Number or Variable.'),
                    BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
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
        return $this->breakAtToken;
    }

    /**
     * @return Mailcode_Variables_Variable|int|NULL
     */
    public function getBreakAt()
    {
        $token = $this->getBreakAtToken();
        if($token === null) {
            return null;
        }

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            return $token->getVariable();
        }

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number) {
            return (int)$token->getValue();
        }

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            return (int)$token->getText();
        }

        return null;
    }
}
