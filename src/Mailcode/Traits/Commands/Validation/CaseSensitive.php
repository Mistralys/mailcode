<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Variable} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of the `insensitive:` keyword in the command statement,
 * and sets the case insensitive flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_CaseSensitive
{
   /**
    * @var boolean
    */
    protected $caseInsensitive = false;

   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    */
    protected $caseToken;
    
    protected function validateSyntax_case_sensitive() : void
    {
        $val = $this->validator->createKeyword('insensitive');
        
        $this->caseInsensitive = $val->isValid();
        
        if($val->isValid())
        {
            $this->caseToken = $val->getToken();
        }
    }
    
    public function isCaseInsensitive() : bool
    {
        return $this->caseInsensitive;
    }
    
    public function getCaseToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if($this->caseToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $this->caseToken;
        }
        
        return null;
    }
}
