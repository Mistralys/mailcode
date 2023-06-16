<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\TimezoneTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\TimezoneTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use function Mailcode\t;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see TimezoneInterface
 */
trait TimezoneTrait
{
    private bool $timezoneEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token $timezoneToken = null;

    protected function validateSyntax_check_timezone(): void
    {
        $tokens = $this->requireParams()->getInfo()->getTokens();

        if (count($tokens) > 2) {
            $this->timezoneToken = $tokens[2];

            if (!$this->timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral &&
                !$this->timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
                $this->validationResult->makeError(
                    t('Invalid timezone:') . ' ' . t('Expected a string or variable.'),
                    TimezoneInterface::VALIDATION_TIMEZONE_CODE_WRONG_TYPE
                );
                return;
            }

            $this->timezoneEnabled = true;
        }
    }

    public function hasTimezone(): bool
    {
        return isset($this->timezoneToken);
    }

    public function getTimezoneToken(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->timezoneToken;
    }
}
