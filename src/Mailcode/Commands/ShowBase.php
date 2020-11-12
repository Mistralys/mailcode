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
abstract class Mailcode_Commands_ShowBase
    extends Mailcode_Commands_Command
    implements
    Mailcode_Commands_Command_Type_Standalone,
    Mailcode_Interfaces_Commands_Variable,
    Mailcode_Interfaces_Commands_URLEncode
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_URLEncode;

    public function supportsURLEncoding() : bool
    {
        return true;
    }

    public function requiresParameters(): bool
    {
        return true;
    }

    public function generatesContent() : bool
    {
        return true;
    }

    public function supportsLogicKeywords() : bool
    {
        return false;
    }

    public function supportsType(): bool
    {
        return false;
    }

    public function getDefaultType() : string
    {
        return '';
    }
}