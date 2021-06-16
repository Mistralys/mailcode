<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Contains} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Contains
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_IfEndsOrBeginsWith
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_CaseSensitive;
    use Mailcode_Traits_Commands_Validation_SearchTerm;
    
    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            Mailcode_Interfaces_Commands_Validation_SearchTerm::VALIDATION_NAME_SEARCH_TERM,
            Mailcode_Interfaces_Commands_Validation_CaseSensitive::VALIDATION_NAME_CASE_SENSITIVE
        );
    }
}
