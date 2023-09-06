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
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowDate;
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
    private bool $timezoneEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token $timezoneToken = null;

    protected function validateSyntax_check_timezone(): void
    {
        $this->timezoneToken = $this->requireParams()
            ->getInfo()
            ->getTokenByParamName(TimezoneInterface::PARAMETER_NAME);

        if ($this->timezoneToken === null) {
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
        $info = $this->requireParams()->getInfo();

        if($default instanceof Mailcode_Variables_Variable) {
            $token = $info->addVariable($default);
        } else {
            $token = $info->addStringLiteral($default);
        }

        $info->setParamName($token, TimezoneInterface::PARAMETER_NAME);

        return $token;
    }

    /**
     * @param Mailcode_Variables_Variable|string|NULL $timezone
     * @return $this
     */
    public function setTimezone($timezone) : self
    {
        $info = $this->requireParams()->getInfo();
        $token = null;

        $existing = $info->getTokenByParamName(TimezoneInterface::PARAMETER_NAME);
        if($existing) {
            $info->removeToken($existing);
        }

        if($timezone instanceof Mailcode_Variables_Variable) {
            $token = $info->addVariable($timezone);
        } else if(is_string($timezone)) {
            $token = $info->addStringLiteral($timezone);
        }

        if($token !== null) {
            $info->setParamName($token, TimezoneInterface::PARAMETER_NAME);
        }

        $this->timezoneToken = $token;

        return $this;
    }
}
