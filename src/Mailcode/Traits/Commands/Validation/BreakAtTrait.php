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

use Mailcode\Commands\CommandException;
use Mailcode\Commands\ParamsException;
use Mailcode\Interfaces\Commands\Validation\BreakAtInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_StringLiterals;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
use function Mailcode\t;

/**
 * Command validation drop-in: checks for the presence
 * of the `break-at` parameter in the command statement.
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
        $this->breakAtToken = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(BreakAtInterface::PARAMETER_NAME);

        if($this->breakAtToken === null) {
            $this->breakAtEnabled = false;
            return;
        }

        if (!$this->breakAtToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number &&
            !$this->breakAtToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $this->validationResult->makeError(
                t('Invalid break-at value:') . ' ' . t('Expected a number or variable.'),
                BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
            );
            return;
        }

        $this->breakAtEnabled = true;
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

    /**
     * @param Mailcode_Variables_Variable|number|NULL $breakAt
     * @return $this
     * @throws CommandException {@see BreakAtInterface::ERROR_INVALID_BREAK_AT_VALUE}
     * @throws ParamsException
     */
    public function setBreakAt($breakAt) : self
    {
        $info = $this->requireParams()->getInfo();

        if(isset($this->breakAtToken)) {
            $info->removeToken($this->breakAtToken);
        }

        $this->breakAtEnabled = false;
        $this->breakAtToken = null;

        if($breakAt === null) {
            return $this;
        }

        $token = null;

        if(is_numeric($breakAt))
        {
            $token = $info->addNumber((string)(int)$breakAt);
        }
        else if($breakAt instanceof Mailcode_Variables_Variable)
        {
            $token = $info->addVariable($breakAt);
        }

        if($token !== null)
        {
            $info->setParamName($token, BreakAtInterface::PARAMETER_NAME);

            $this->breakAtEnabled = true;
            $this->breakAtToken = $token;
            return $this;
        }

        throw new CommandException(
            'Invalid break-at value',
            sprintf(
                'Expected a number or variable, got: %s',
                $breakAt instanceof Mailcode_Variables_Variable ? $breakAt->getFullName() : $breakAt
            ),
            BreakAtInterface::ERROR_INVALID_BREAK_AT_VALUE
        );
    }
}
