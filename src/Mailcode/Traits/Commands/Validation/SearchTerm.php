<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_SearchTerm} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_SearchTerm
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a string literal supposed to contain a search term.
 * Will accept the first string literal it finds.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 * @see Mailcode_Interfaces_Commands_Validation_SearchTerm
 */
trait Mailcode_Traits_Commands_Validation_SearchTerm
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
     */
    protected $searchTerm;
    
    protected function validateSyntax_search_term() : void
    {
        $val = $this->validator->createStringLiteral();
        
        if($val->isValid())
        {
            $this->searchTerm = $val->getToken();
        }
        else
        {
            $this->validationResult->makeError(
                t('No search term specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            );
        }
    }
    
    public function getSearchTerm() : string
    {
        if($this->searchTerm instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return $this->searchTerm->getNormalized();
        }
        
        throw new Mailcode_Exception(
            'No string literal available',
            null,
            Mailcode_Commands_CommonConstants::ERROR_NO_STRING_LITERAL_AVAILABLE
        );
    }
}
