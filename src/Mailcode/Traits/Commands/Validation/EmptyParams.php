<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_EmptyParams} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_EmptyParams
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: verifies if the command parameters
 * are empty, and if they are required, adds an error accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_Validation_EmptyParams
{
    abstract public function requiresParameters() : bool;

    protected function validateSyntax_params_empty() : void
    {
        if(!$this->requiresParameters())
        {
            return;
        }

        if(empty($this->paramsString))
        {
            $this->validationResult->makeError(
                t('Parameters have to be specified.'),
                Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            );
            return;
        }
    }
}
