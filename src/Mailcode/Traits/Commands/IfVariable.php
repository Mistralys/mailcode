<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Variable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Variable
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * IF for variable comparisons.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfVariable
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_Value;
    use Mailcode_Traits_Commands_Validation_Operand;
    use Mailcode_Traits_Commands_Validation_CaseSensitive;
    
    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            Mailcode_Interfaces_Commands_Validation_Operand::VALIDATION_NAME_OPERAND,
            Mailcode_Interfaces_Commands_Validation_Value::VALIDATION_NAME_VALUE,
            Mailcode_Interfaces_Commands_Validation_CaseSensitive::VALIDATION_NAME_CASE_SENSITIVE
        );
    }
    
   /**
    * @return array<string>
    */
    protected function getAllowedOperands() : array
    {
        return Mailcode_Parser_Statement_Tokenizer_Token_Operand::getComparisonSigns();
    }
}
