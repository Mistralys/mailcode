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

use AppUtils\OperationResult;

/**
 * Mailcode command: opening IF NOTEMPTY statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfNotEmpty
{
    use Mailcode_Traits_Commands_Validation_Variable;

    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE
        );
    }
}
