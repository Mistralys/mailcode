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

/**
 * IF for variable comparisons.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfVariable
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_Value;
    use Mailcode_Traits_Commands_Validation_Operand;
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'operand',
            'value'
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
