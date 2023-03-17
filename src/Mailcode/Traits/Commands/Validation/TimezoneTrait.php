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
     * @var string
     */
    protected string $timezone = '';

    protected function validateSyntax_check_timezone(): void
    {
        // first, check if we have an explicit timezone
        $tokens = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        if (sizeof($tokens) > 1) {
            $this->timezone = '"' . $tokens[1]->getText() . '"';
            return;
        }

        // then, check if a variable is used for timezone
        $variables = $this->requireParams()
            ->getInfo()
            ->getVariables();

        if (sizeof($variables) > 1) {
            $this->timezone = $variables[1]->getFullName();
            return;
        }

        // neither explicit timezone nor variable present, so use nothing
        $this->timezone= '';
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
