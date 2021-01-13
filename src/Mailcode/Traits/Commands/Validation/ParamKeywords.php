<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_ParamKeywords} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_ParamKeywords
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: parses any keywords present in
 * the command parameters, and validates them.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property OperationResult $validationResult
 * @property string $paramsString
 * @property Mailcode_Commands_LogicKeywords $logicKeywords
 */
trait Mailcode_Traits_Commands_Validation_ParamKeywords
{
    abstract public function supportsLogicKeywords() : bool;

    protected function validateSyntax_params_keywords() : void
    {
        if(!$this->supportsLogicKeywords())
        {
            return;
        }

        $this->logicKeywords = new Mailcode_Commands_LogicKeywords($this, $this->paramsString);

        if(!$this->logicKeywords->isValid())
        {
            $this->validationResult->makeError(
                t('Invalid parameters:').' '.$this->logicKeywords->getErrorMessage(),
                $this->logicKeywords->getCode()
            );

            return;
        }

        $this->paramsString = $this->logicKeywords->getMainParamsString();
    }
}
