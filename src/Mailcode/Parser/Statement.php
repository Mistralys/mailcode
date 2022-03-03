<?php
/**
 * File containing the {@see Mailcode_Parser_Statement} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;
use Mailcode\Parser\Statement\Tokenizer\EventHandler;
use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

/**
 * Mailcode statement parser: parses arbitrary statements
 * to check for validation issues.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement
{
    public const VALIDATION_EMPTY = 48801;
    public const VALIDATION_UNQUOTED_STRING_LITERALS = 48802;
    
   /**
    * @var string
    */
    protected string $statement;
    
   /**
    * @var OperationResult
    */
    protected OperationResult $result;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer
    */
    protected Mailcode_Parser_Statement_Tokenizer $tokenizer;
    
   /**
    * @var Mailcode_Parser_Statement_Info|NULL
    */
    protected ?Mailcode_Parser_Statement_Info $info = null;

    /**
     * @var bool
     */
    protected bool $freeform = false;

    /**
     * @var Mailcode_Commands_Command|null
     */
    private ?Mailcode_Commands_Command $sourceCommand = null;

    public function __construct(string $statement, bool $freeform=false, ?Mailcode_Commands_Command $sourceCommand=null)
    {
        $this->sourceCommand = $sourceCommand;
        $this->statement = $this->prepareStatement($statement);
        $this->result = new OperationResult($this);
        $this->tokenizer = new Mailcode_Parser_Statement_Tokenizer($this);
        $this->freeform = $freeform;

        $this->validate();
    }

    private function prepareStatement(string $statement) : string
    {
        return str_replace(
            array(
                SpecialChars::PLACEHOLDER_BRACKET_OPEN,
                SpecialChars::PLACEHOLDER_BRACKET_CLOSE
            ),
            array(
                '\{',
                '\}'
            ),
            $statement
        );
    }

    public function getSourceCommand() : ?Mailcode_Commands_Command
    {
        return $this->sourceCommand;
    }

    public function getStatementString() : string
    {
        return $this->statement;
    }
    
    public function isValid() : bool
    {
        $this->validate();

        return $this->result->isValid();
    }
    
    public function getValidationResult() : OperationResult
    {
        return $this->result;
    }

    public function getEventHandler() : EventHandler
    {
        return $this->tokenizer->getEventHandler();
    }
    
    public function getInfo() : Mailcode_Parser_Statement_Info
    {
        if($this->info instanceof Mailcode_Parser_Statement_Info)
        {
            return $this->info; 
        }
        
        $this->info = new Mailcode_Parser_Statement_Info($this->tokenizer);
        
        return $this->info;
    }
    
    protected function validate() : void
    {
        if($this->freeform)
        {
            return;
        }

        if(!$this->tokenizer->hasTokens())
        {
            $this->result->makeError(
                t('Empty statement'),
                self::VALIDATION_EMPTY
            );
            
            return;
        }
        
        $unknown = $this->tokenizer->getFirstUnknown();
        
        if($unknown)
        {
            $this->result->makeError(
               t('Unquoted string literal found:').' ('.htmlspecialchars($unknown->getMatchedText()).')',
                self::VALIDATION_UNQUOTED_STRING_LITERALS
            );
        }
    }
    
    public function getNormalized() : string
    {
        return $this->tokenizer->getNormalized();
    }

    /**
     * Creates a copy of the parameters, with the exact
     * same parameters and source command, if any.
     *
     * @return Mailcode_Parser_Statement
     */
    public function copy() : Mailcode_Parser_Statement
    {
        return new Mailcode_Parser_Statement(
            $this->getNormalized(),
            $this->freeform,
            $this->sourceCommand
        );
    }
}
