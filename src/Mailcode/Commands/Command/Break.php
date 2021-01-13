<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Break} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Break
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: A BREAK command to stop in loops.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_Break extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    const VALIDATION_NO_PARENT_FOR = 75701;

    public function getName() : string
    {
        return 'break';
    }
    
    public function getLabel() : string
    {
        return t('Loop break');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding(): bool
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

    public function generatesContent(): bool
    {
        return false;
    }

    protected function getValidations(): array
    {
        return array();
    }

    protected function _validateNesting() : void
    {
        if($this->findParentFor($this))
        {
            return;
        }

        $this->validationResult->makeError(
            t('A break command must only be used within a %1$s command.', 'FOR'),
            self::VALIDATION_NO_PARENT_FOR
        );
    }

    protected function findParentFor(Mailcode_Commands_Command $command) : bool
    {
        $parent = $command->getParent();

        if($parent === null)
        {
            return false;
        }

        if($parent instanceof Mailcode_Commands_Command_For)
        {
            return true;
        }

        return $this->findParentFor($parent);
    }
}
