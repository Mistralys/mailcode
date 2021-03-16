<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_Mono} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_Mono
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening CODE statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_Mono
    extends Mailcode_Commands_Command
    implements
        Mailcode_Commands_Command_Type_Opening,
        Mailcode_Interfaces_Commands_ProtectedContent,
        Mailcode_Interfaces_Commands_Multiline
{
    use Mailcode_Traits_Commands_ProtectedContent;
    use Mailcode_Traits_Commands_Validation_Multiline;

    public function getName() : string
    {
        return 'mono';
    }
    
    public function getLabel() : string
    {
        return t('Format text as monospaced');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding() : bool
    {
        return false;
    }

    public function getDefaultType() : string
    {
        return '';
    }
    
    public function requiresParameters(): bool
    {
        return false;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }
    
    protected function getValidations() : array
    {
        return array(
            'multiline'
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
