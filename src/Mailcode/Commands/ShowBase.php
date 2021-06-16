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
    Mailcode_Interfaces_Commands_Validation_Variable,
    Mailcode_Interfaces_Commands_Validation_URLEncode,
    Mailcode_Interfaces_Commands_Validation_URLDecode
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_URLEncode;
    use Mailcode_Traits_Commands_Validation_URLDecode;

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

    protected function resolveValidations(): array
    {
        $validations = parent::resolveValidations();
        $validations[] = 'urldeencode';

        return $validations;
    }

    protected function validateSyntax_urldeencode() : void
    {
        if($this->isURLEncoded() && $this->getURLDecodeToken() !== null)
        {
            $this->validationResult->makeError(
                t('Cannot enable URL decoding and encoding at the same time.'),
                Mailcode_Commands_CommonConstants::VALIDATION_URL_DE_AND_ENCODE_ENABLED
            );
        }
    }
}