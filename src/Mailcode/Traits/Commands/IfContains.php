<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Contains} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Contains
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfContains
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
     */
    protected $variableToken;

   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
    */
    protected $stringToken;
    
   /**
    * @var boolean
    */
    protected $caseInsensitive = false;
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'literal',
        );
    }
    
    protected function validateSyntax_variable() : void
    {
        $info = $this->params->getInfo();
        
        $variable = $info->getVariableByIndex(0);
        
        if($variable !== null)
        {
            $this->variableToken = $variable;
            return;
        }
        
        $this->validationResult->makeError(
            t('No variable specified in the command.'),
            Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
        );
    }
    
    protected function validateSyntax_literal() : void
    {
        $info = $this->params->getInfo();
        
        // first variant: variable "Search term"
        $string = $info->getStringLiteralByIndex(1);
        
        if($string !== null)
        {
            $this->stringToken = $string;
            return;
        }
        
        $this->validateSyntax_keyword();
    }
    
    protected function validateSyntax_keyword() : void
    {
        $info = $this->params->getInfo();
        
        // second variant: variable insensitive: "Search term"
        $keyword = $info->getKeywordByIndex(1);
        
        if($keyword === null)
        {
            $this->validationResult->makeError(
                t('Expected a search term or the %1$s keyword after the variable name.', 'insensitive:'),
                Mailcode_Commands_IfBase::VALIDATION_EXPECTED_KEYWORD
            );
            
            return;
        }
        
        if($keyword->getKeyword() !== 'insensitive:')
        {
            $this->validationResult->makeError(
                t('Invalid keyword %1$s.', $keyword->getKeyword()).' '.
                t('Only the keyword %1$s may be used here.', 'insensitive:'),
                Mailcode_Commands_IfBase::VALIDATION_INVALID_KEYWORD
            );
            
            return;
        }
        
        $this->caseInsensitive = true;
        
        $this->validateSyntax_literal_after_keyword();
    }
    
    protected function validateSyntax_literal_after_keyword() : void
    {
        $info = $this->params->getInfo();
        
        $string = $info->getStringLiteralByIndex(2);
        
        if($string !== null)
        {
            $this->stringToken = $string;
            return;
        }
        
        $this->validationResult->makeError(
            t('Expected a search term after the %1$s keyword.', 'insensitive:'),
            Mailcode_Commands_IfBase::VALIDATION_CONTAINS_MISSING_SEARCH_TERM
        );
    }

   /**
    * Retrieves the variable being compared.
    *
    * @return Mailcode_Variables_Variable
    */
    public function getVariable() : Mailcode_Variables_Variable
    {
        if($this->variableToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $this->variableToken->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No variable available',
            null,
            Mailcode_Commands_IfBase::ERROR_NO_VARIABLE_AVAILABLE
        );
    }
    
    public function isCaseInsensitive() : bool
    {
        return $this->caseInsensitive;
    }
    
    public function getSearchTerm() : string
    {
        if($this->stringToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return $this->stringToken->getNormalized();
        }
        
        throw new Mailcode_Exception(
            'No string literal available',
            null,
            Mailcode_Commands_IfBase::ERROR_NO_STRING_LITERAL_AVAILABLE
        );
    }
}
