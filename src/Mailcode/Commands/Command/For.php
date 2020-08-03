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
    const ERROR_SOURCE_VARIABLE_NOT_AVAILABLE = 64101;
    const ERROR_LOOP_VARIABLE_NOT_AVAILABLE = 64102;
    
    const VALIDATION_INVALID_FOR_STATEMENT = 49701;
    const VALIDATION_WRONG_KEYWORD = 49702;
    const VALIDATION_VARIABLE_NAME_IS_THE_SAME = 49703;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    private $loopVar;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    private $sourceVar;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
    */
    private $keyword;
    
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
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }
    
    protected function getValidations() : array
    {
        return array(
            'statement',
            'keyword',
            'variable_names'
        );
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    public function getSourceVariable() : Mailcode_Variables_Variable
    {
        if(isset($this->sourceVar))
        {
            return $this->sourceVar->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No source variable available',
            null,
            self::ERROR_SOURCE_VARIABLE_NOT_AVAILABLE
        );
    }
    
    public function getLoopVariable() : Mailcode_Variables_Variable
    {
        if(isset($this->loopVar))
        {
            return $this->loopVar->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No loop variable available',
            null,
            self::ERROR_LOOP_VARIABLE_NOT_AVAILABLE
        );
    }
    
    protected function validateSyntax_statement() : void
    {
        $info = $this->params->getInfo();
        
        $this->loopVar = $info->getVariableByIndex(0);
        $this->keyword = $info->getKeywordByIndex(1);
        $this->sourceVar = $info->getVariableByIndex(2);
        
        if(!$this->loopVar || !$this->keyword || !$this->sourceVar)
        {
            $this->validationResult->makeError(
                t('Not a valid for loop.').' '.t('Is the %1$s keyword missing?', 'in:'),
                self::VALIDATION_INVALID_FOR_STATEMENT
            );
            
            return;
        }
    }
    
    protected function validateSyntax_keyword() : void
    {
        if($this->keyword->isForIn())
        {
            return;
        }
         
        $this->validationResult->makeError(
            t('The %1$s keyword cannot be used in this command.', $this->keyword->getKeyword()),
            self::VALIDATION_WRONG_KEYWORD
        );
    }
    
    protected function validateSyntax_variable_names() : void
    {
        if($this->sourceVar->getVariable()->getFullName() !== $this->loopVar->getVariable()->getFullName())
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('The source and loop variables have the same name.'),
            self::VALIDATION_VARIABLE_NAME_IS_THE_SAME
        );
    }
}
