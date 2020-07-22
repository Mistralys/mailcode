<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_IfEmpty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Traits_Commands_IfEmpty
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF EMPTY statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_IfEmpty
{
    use Mailcode_Traits_Commands_Validation_Variable;

    protected function getValidations() : array
    {
        return array(
            'variable'
        );
    }
}
