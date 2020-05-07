<?php
/**
 * File containing the {@see Mailcode_Commands_IfBase} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_IfBase
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for IF commands (IF, ELSEIF).
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Commands_IfBase extends Mailcode_Commands_Command
{
    const VALIDATION_VARIABLE_MISSING = 49201;
    const VALIDATION_CONTAINS_MISSING_SEARCH_TERM = 49202;
    const VALIDATION_INVALID_KEYWORD = 49203;
    const VALIDATION_OPERAND_MISSING = 49204;
    const VALIDATION_OPERAND_NOT_COMPARISON = 49205;
    const VALIDATION_INVALID_COMPARISON_TOKEN = 49206;
    const VALIDATION_EXPECTED_KEYWORD = 49207;
    const VALIDATION_NOTHING_AFTER_OPERAND = 49208;
    
    const ERROR_NO_VARIABLE_AVAILABLE = 52601;
    const ERROR_NO_STRING_LITERAL_AVAILABLE = 52602;
    const ERROR_NO_COMPARATOR_AVAILABLE = 52603;
    const ERROR_NO_VALUE_AVAILABLE = 52604;
    
    public function supportsType(): bool
    {
        return true;
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    public function isCommand() : bool
    {
        return $this->type === 'command' || empty($this->type);
    }
    
    public function isVariable() : bool
    {
        return $this->type === 'variable';
    }
    
    public function isContains() : bool
    {
        return $this->type === 'contains';
    }
    
    protected function getValidations() : array
    {
        return array();
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    public function getSupportedTypes() : array
    {
        return array(
            'variable',
            'command',
            'contains'
        );
    }
    
    public function getDefaultType() : string
    {
        return 'command';
    }
}
