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
class Mailcode_Commands_Command_Comment extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
   /**
    * @var string
    */
    private $commentString = '';
    
    protected function init() : void
    {
        $this->commentString = trim(trim($this->paramsString), '"');
        $this->paramsString = '"Dummy"'; // so the command does not complain that it is empty
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
        return true;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }

    protected function getValidations() : array
    {
        return array(
            'comment'
        );
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    public function getCommentString() : string
    {
        return $this->commentString;
    }
    
    protected function validateSyntax_comment() : void
    {
        if(empty($this->commentString))
        {
            $this->validationResult->makeError(
                t('The comment text ist empty.'),
                Mailcode_Commands_CommonConstants::VALIDATION_COMMENT_MISSING
            );
        }
    }
}
