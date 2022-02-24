<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_TypeSupported}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_TypeSupported
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_TypeSupported
 */
interface Mailcode_Interfaces_Commands_Validation_TypeSupported extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_TYPE_SUPPORTED = 'type_supported';
}
