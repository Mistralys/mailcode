<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_Variable}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_Variable
 */
interface Mailcode_Interfaces_Commands_Validation_Variable extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_VARIABLE = 'variable';
    public const VALIDATION_NAME_VARIABLE_OPTIONAL = 'variable_optional';

    public function getVariable(): Mailcode_Variables_Variable;

    public function getVariableName(): string;

    public function isInLoop(): bool;

    public function getLoopCommand(): ?Mailcode_Commands_Command_For;
}
