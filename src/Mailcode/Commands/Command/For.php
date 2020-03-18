<?php
/**
 * File containing the {@see Mailcode_Commands_Command_For} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_For
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening FOR statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_For extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Opening
{
    const VALIDATION_INVALID_FOR_STATEMENT = 49701;
    
    public function getName() : string
    {
        return 'for';
    }
    
    public function getLabel() : string
    {
        return t('FOR loop');
    }
    
    public function supportsType(): bool
    {
        return false;
    }
    
    public function getDefaultType() : string
    {
        return '';
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    protected function getValidations() : array
    {
        return array(
            'statement'
        );
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    protected function validateSyntax_statement() : void
    {
        $info = $this->params->getInfo();
        
        $variable = $info->getVariableByIndex(0);
        $keyword = $info->getKeywordByIndex(1);
        $container = $info->getVariableByIndex(2);
        
        if($variable && $keyword && $container && $keyword->isForIn())
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('Not a valid for loop.').' '.t('Is the %1$s keyword missing?', 'in:'),
            self::VALIDATION_INVALID_FOR_STATEMENT
        );
    }
}
