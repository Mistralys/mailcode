<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_TypeUnsupported} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_TypeUnsupported
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: ensures that no type is specified if the command
 * does not support types.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property OperationResult $validationResult
 * @property string $type
 */
trait Mailcode_Traits_Commands_Validation_TypeUnsupported
{
    abstract public function supportsType() : bool;

    protected function validateSyntax_type_unsupported() : void
    {
        if($this->supportsType() || empty($this->type))
        {
            return;
        }

        $this->validationResult->makeError(
            t('Command addons are not supported (the %1$s part).', $this->type),
            Mailcode_Commands_Command::VALIDATION_ADDONS_NOT_SUPPORTED
        );
    }
}
