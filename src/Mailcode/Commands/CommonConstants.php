<?php 
/**
 * File containing the {@see Mailcode_Commands_CommonConstants} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_CommonConstants
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Container for validation and error constants that are used
 * by a number of commands. The validation traits all reference
 * these, for example.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_CommonConstants
{
    public const VALIDATION_VARIABLE_MISSING = 49201;
    public const VALIDATION_SEARCH_TERM_MISSING = 49202;
    public const VALIDATION_INVALID_KEYWORD = 49203;
    public const VALIDATION_OPERAND_MISSING = 49204;
    public const VALIDATION_INVALID_OPERAND = 49205;
    public const VALIDATION_INVALID_COMPARISON_TOKEN = 49206;
    public const VALIDATION_EXPECTED_KEYWORD = 49207;
    public const VALIDATION_NOTHING_AFTER_OPERAND = 49208;
    public const VALIDATION_STRING_LITERAL_MISSING = 49209;
    public const VALIDATION_VALUE_MISSING = 49210;
    public const VALIDATION_COMMENT_MISSING = 49211;
    public const VALIDATION_VALUE_NOT_NUMERIC = 49212;
    public const VALIDATION_URL_DE_AND_ENCODE_ENABLED = 49213;

    public const ERROR_NO_VARIABLE_AVAILABLE = 52601;
    public const ERROR_NO_STRING_LITERAL_AVAILABLE = 52602;
    public const ERROR_NO_COMPARATOR_AVAILABLE = 52603;
    public const ERROR_NO_VALUE_AVAILABLE = 52604;
    public const ERROR_NO_OPERAND_AVAILABLE = 52605;
}
