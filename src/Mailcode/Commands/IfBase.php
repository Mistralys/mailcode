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
    public function supportsType(): bool
    {
        return true;
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    public function supportsLogicKeywords() : bool
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
            'contains',
            'empty',
            'not-empty',
            'begins-with',
            'ends-with'
        );
    }
    
    public function getDefaultType() : string
    {
        return 'command';
    }
}
