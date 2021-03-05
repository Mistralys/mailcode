<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_ParseParams} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_ParseParams
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: parses the command's parameters
 * string into its constituent parts.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property OperationResult $validationResult
 * @property string $paramsString
 * @property Mailcode $mailcode
 * @property Mailcode_Parser_Statement $params
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_ParseParams
{
    abstract public function requiresParameters() : bool;

    abstract public function hasFreeformParameters() : bool;

    protected function validateSyntax_params_parse() : void
    {
        $this->params = $this->mailcode->getParser()->createStatement(
            $this->paramsString,
            $this->hasFreeformParameters(),
            $this
        );

        if(!$this->params->isValid() && $this->requiresParameters())
        {
            $error = $this->params->getValidationResult();

            $this->validationResult->makeError(
                t('Invalid parameters:').' '.$error->getErrorMessage(),
                Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            );

            return;
        }

        $this->validator = new Mailcode_Parser_Statement_Validator($this->params);
    }
}
