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
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_IfContains
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_CaseSensitive;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
    */
    protected $searchTerms = array();
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'case_sensitive',
            'search_terms'
        );
    }
    
    protected function validateSyntax_search_terms() : void
    {
        $tokens = $this->params->getInfo()->createPruner()
        ->limitToStringLiterals()
        ->getTokens();
        
        foreach($tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
            {
                $this->searchTerms[] = $token;
            }
        }
        
        if(empty($this->searchTerms))
        {
            $this->validationResult->makeError(
                t('No search terms found:').' '.
                t('At least one search term has to be specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            );
        }
    }
    
   /**
    * Retrieves all search terms.
    * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
    */
    public function getSearchTerms() : array
    {
        return $this->searchTerms;
    }
}
