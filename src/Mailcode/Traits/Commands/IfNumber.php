<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Traits_Commands_IfNumber} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Traits_Commands_IfNumber
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF NUMBER statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_IfNumber
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_Value;

    protected function getValidations() : array
    {
        return array(
            'variable',
            'value',
            'numeric_value'
        );
    }

    public function getNumber() : float
    {
        return floatval($this->getRawNumber());
    }

    protected function getRawNumber() : string
    {
        return str_replace(array(',', ' '), array('.', ''), trim($this->getValue(), '"'));
    }

    protected function validateSyntax_numeric_value(): void
    {
        $value = $this->getRawNumber();

        if(!is_numeric($value))
        {
            $this->validationResult->makeError(
                t(
                    '%1$s is not a valid numeric value.',
                    '"'.trim($this->getValue(), '"').'"'
                ),
                Mailcode_Commands_CommonConstants::VALIDATION_VALUE_NOT_NUMERIC
            );
        }
    }
}
