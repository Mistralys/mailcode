<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Comment} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Comment
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: Add a comment.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_Comment extends Mailcode_Commands_Command_Type_Standalone
{
    protected function init() : void
    {
        // automatically quote the parameters, since comments don't require any.
        if(!strstr($this->paramsString, '"'))
        {
            $this->paramsString = '"'.$this->paramsString.'"';
        }
    }
    
    public function getName() : string
    {
        return 'comment';
    }
    
    public function getLabel() : string
    {
        return t('Add a comment');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function requiresParameters(): bool
    {
        return true;
    }

    protected function getValidations() : array
    {
        return array();
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
}
