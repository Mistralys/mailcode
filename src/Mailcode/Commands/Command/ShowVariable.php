<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowVariable extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_URLEncode;

    const VALIDATION_TOO_MANY_PARAMETERS = 69701;

    public function getName() : string
    {
        return 'showvar';
    }
    
    public function getLabel() : string
    {
        return t('Show variable');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding() : bool
    {
        return true;
    }
    
    public function getDefaultType() : string
    {
        return '';
    }

    public function requiresParameters(): bool
    {
        return true;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'urlencode',
            'no_other_tokens'
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }

    protected function validateSyntax_no_other_tokens() : void
    {
        $tokens = $this->params->getInfo()->getTokens();

        $count = 1;

        if(isset($this->urlencodeToken))
        {
            $count = 2;
        }

        if(count($tokens) > $count)
        {
            $this->validationResult->makeError(
                t('Unknown parameters found:').' '.
                t('Only the variable name should be specified.'),
                self::VALIDATION_TOO_MANY_PARAMETERS
            );
        }
    }
}
