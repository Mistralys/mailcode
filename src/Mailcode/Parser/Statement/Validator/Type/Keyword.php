<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Validator_Type_Variable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Parser_Statement_Validator_Type_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Used to validate a single variable token in a command's parameters.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Validator_Type_Keyword extends Mailcode_Parser_Statement_Validator_TokenType
{
    public const ERROR_NO_KEYWORD_TOKEN_FOUND = 62701;
    
   /**
    * The name of the keyword, with ":" appended.
    * @var string
    */
    protected $keywordName;
    
    public function __construct(Mailcode_Parser_Statement $statement, string $keywordName)
    {
        $this->keywordName = rtrim($keywordName, ':').':';
        
        parent::__construct($statement);
    }
    
    protected function getTokenClass() : string
    {
        return Mailcode_Parser_Statement_Tokenizer_Token_Keyword::class;
    }
    
    protected function _isMatch(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool
    {
        return
        $token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword 
            && 
        $token->getKeyword() === $this->keywordName;
    }
    
    public function getToken() : Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if($this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $this->token;
        }
        
        throw new Mailcode_Exception(
            'No keyword token found.',
            'Cannot retrieve keyword token, none was found in the command. To avoid this error, do not use the getToken command when the command is invalid.',
            self::ERROR_NO_KEYWORD_TOKEN_FOUND
        );
    }
}
