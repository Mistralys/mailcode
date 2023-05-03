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

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see TimezoneInterface
 */
trait TimezoneTrait
{
    /**
     * The timezone
     * @var string|NULL
     */
    protected ?string $timezoneString = null;
    private ?string $timezoneVariable = null;

    protected function validateSyntax_check_timezone(): void
    {
        // first, check if we have an explicit timezone
        $tokens = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        if (count($tokens) > 1) {
            $this->timezoneString = $tokens[1]->getText();
            return;
        }

        // then, check if a variable is used for timezone
        $variables = $this->requireParams()
            ->getInfo()
            ->getVariables();

        if (count($variables) > 1) {
            $this->timezoneVariable = $variables[1]->getFullName();
            return;
        }

        // neither explicit timezone nor variable present, so use nothing
        $this->timezoneString= null;
        $this->timezoneVariable = null;
    }

    public function getTimezoneString(): ?string
    {
        return $this->timezoneString;
    }

    public function getTimezoneVariable() : ?string
    {
        return $this->timezoneVariable;
    }

    public function hasTimezone() : bool
    {
        return isset($this->timezoneString) || isset($this->timezoneVariable);
    }
}
