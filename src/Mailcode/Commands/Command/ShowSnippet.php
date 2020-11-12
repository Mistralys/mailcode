<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowSnippet} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowSnippet
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
class Mailcode_Commands_Command_ShowSnippet extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_URLEncode;
    
    public function getName() : string
    {
        return 'showsnippet';
    }
    
    public function getLabel() : string
    {
        return t('Show text snippet');
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
            'urlencode'
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
