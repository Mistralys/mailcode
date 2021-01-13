<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_TypeSupported} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_TypeSupported
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: ensures that the type is valid
 * if the command supports types, and a type is present.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property OperationResult $validationResult
 * @property string $type
 */
trait Mailcode_Traits_Commands_Validation_TypeSupported
{
    /**
     * @return string[]
     */
    abstract public function getSupportedTypes() : array;

    abstract public function supportsType() : bool;

    protected function validateSyntax_type_supported() : void
    {
        if(!$this->supportsType() || empty($this->type))
        {
            return;
        }

        $types = $this->getSupportedTypes();

        if(!in_array($this->type, $types))
        {
            $this->validationResult->makeError(
                t('The command addon %1$s is not supported.', $this->type).' '.
                t('Valid addons are %1$s.', implode(', ', $types)),
                Mailcode_Commands_Command::VALIDATION_ADDON_NOT_SUPPORTED
            );

            return;
        }
    }
}
