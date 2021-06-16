<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Multiline} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Multiline
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of the `multiline:` keyword in the command statement,
 * and sets the multiline flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 * @see Mailcode_Interfaces_Commands_Validation_Multiline
 */
trait Mailcode_Traits_Commands_Validation_Multiline
{
   /**
    * @var boolean
    */
    protected $multiline = false;

   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
    */
    protected $multilineToken;
    
    protected function validateSyntax_multiline() : void
    {
        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_MULTILINE);
        
        $this->multiline = $val->isValid();
        
        if($val->isValid())
        {
            $this->multilineToken = $val->getToken();
        }
    }
    
    public function isMultiline() : bool
    {
        return $this->multiline;
    }
    
    public function getMultilineToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if($this->multilineToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $this->multilineToken;
        }
        
        return null;
    }
}
