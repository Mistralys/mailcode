<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Info_Pruner} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Info_Pruner
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Statement pruning utility: allows applying rules to
 * an existing statement's tokens to retrieve only the
 * relevant tokens.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Info_Pruner
{
   /**
    * @var Mailcode_Parser_Statement_Info
    */
    private $info;
    
   /**
    * @var string[]
    */
    private $exclude = array();
    
   /**
    * @var string[]
    */
    private $classNames = array();
    
    public function __construct(Mailcode_Parser_Statement_Info $info)
    {
        $this->info = $info;
    }
    
    public function excludeToken(Mailcode_Parser_Statement_Tokenizer_Token $token) : Mailcode_Parser_Statement_Info_Pruner
    {
        $id = $token->getID();
        
        if(!in_array($id, $this->exclude))
        {
            $this->exclude[] = $id;
        }
        
        return $this;
    }
    
    public function limitToStringLiterals() : Mailcode_Parser_Statement_Info_Pruner
    {
        return $this->limitByClassName(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class);
    }
    
    public function limitToOperands() : Mailcode_Parser_Statement_Info_Pruner
    {
        return $this->limitByClassName(Mailcode_Parser_Statement_Tokenizer_Token_Operand::class);
    }

    public function limitToNumbers() : Mailcode_Parser_Statement_Info_Pruner
    {
        return $this->limitByClassName(Mailcode_Parser_Statement_Tokenizer_Token_Number::class);
    }
    
    public function limitByClassName(string $className) : Mailcode_Parser_Statement_Info_Pruner
    {
        if(!in_array($className, $this->classNames))
        {
            $this->classNames[] = $className;
        }
        
        return $this;
    }
    
   /**
    * Retrieves all tokens in the statement matching the
    * current criteria.
    * 
    * @return Mailcode_Parser_Statement_Tokenizer_Token[]
    */
    public function getTokens() : array
    {
        $tokens = $this->info->getTokens();
        $keep = array(); 
        
        foreach($tokens as $token)
        {
            if(in_array($token->getID(), $this->exclude))
            {
                continue;
            }
            
            $keep[] = $token;
        }
        
        return $keep;
    }
}
