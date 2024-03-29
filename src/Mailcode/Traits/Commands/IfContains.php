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

use AppUtils\OperationResult;

/**
 * Mailcode command: opening IF statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
trait Mailcode_Traits_Commands_IfContains
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_CaseSensitive;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral[]
    */
    protected array $searchTerms = array();
    
    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            Mailcode_Interfaces_Commands_Validation_CaseSensitive::VALIDATION_NAME_CASE_SENSITIVE,
            'search_terms'
        );
    }
    
    protected function validateSyntax_search_terms() : void
    {
        $tokens = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        foreach($tokens as $token)
        {
            $this->searchTerms[] = $token;
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
