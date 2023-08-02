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
use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
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
    private ?Mailcode_Parser_Statement_Tokenizer_Token $timezoneToken = null;

    protected function validateSyntax_check_timezone(): void
    {
        $this->timezoneToken = $this->requireParams()
            ->getInfo()
            ->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_TIMEZONE);

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_TIMEZONE);

        if ($this->timezoneToken === null || !$val->isValid()) {
            return;
        }

        if (!$this->timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral &&
            !$this->timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $this->validationResult->makeError(
                t('Invalid timezone:') . ' ' . t('Expected a string or variable.'),
                TimezoneInterface::VALIDATION_TIMEZONE_CODE_WRONG_TYPE
            );
        }
    }

    /**
     * Gets the time zone to use for the command. If none has
     * been specified in the original command, the default
     * time zone is used as defined via {@see Mailcode_Commands_Command_ShowDate::setDefaultTimezone()}.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    public function getTimezoneToken(): Mailcode_Parser_Statement_Tokenizer_Token
    {
        if(!isset($this->timezoneToken)) {
            $this->timezoneToken = $this->createTimeZoneToken();
        }

        return $this->timezoneToken;
    }

    /**
     * Creates the default time zone token on demand.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    private function createTimeZoneToken() : Mailcode_Parser_Statement_Tokenizer_Token
    {
        $default = Mailcode_Commands_Command_ShowDate::getDefaultTimezone();

        if($default instanceof Mailcode_Variables_Variable) {
            return new Mailcode_Parser_Statement_Tokenizer_Token_Variable(
                'showdate-timezone-token',
                $default->getFullName(),
                $default,
                $this
            );
        }

        return new Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral(
            'showdate-timezone-token',
            $default,
            null,
            $this
        );
    }
}
