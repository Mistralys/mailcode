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
    const ERROR_TOKENIZE_METHOD_MISSING = 48901;
    
    const VALIDATION_EMPTY = 48801;
    
    const VALIDATION_UNQUOTED_STRING_LITERALS = 48802;
    
   /**
    * @var string
    */
    protected $statement;
    
   /**
    * @var OperationResult
    */
    protected $result;
    
    protected $operands = array(
        '==',
        '<=',
        '>=',
        '!=',
        '=',
        '+',
        '-',
        '/',
        '*'
    );
    
    public function __construct(string $statement)
    {
        $this->statement = $statement;
    }
    
    public function isValid() : bool
    {
        return $this->getValidationResult()->isValid();
    }
    
    public function getValidationResult() : OperationResult
    {
        if(isset($this->result))
        {
            return $this->result;
        }
        
        $this->result = new OperationResult($this);
        
        $this->validate();
        
        return $this->result;
    }
    
    protected function validate() : void
    {
        $statement = trim($this->statement);
        
        if(empty($statement))
        {
            $this->result->makeError(
                t('Empty statement'),
                self::VALIDATION_EMPTY
            );
            
            return;
        }
        
        $tokenized = $this->tokenize($statement);        
        
        $leftover = $this->removeTokens($tokenized);
        $leftover = str_replace(' ', '', $leftover);
         
        if(!empty($leftover))
        {
            $this->result->makeError(
               t('Unquoted string literals found:').' "'.$leftover.'"',
                self::VALIDATION_UNQUOTED_STRING_LITERALS
            );
        }
        
        /*
        echo PHP_EOL;
        print_r(array(
            'statement' => $this->statement,
            'tokenized' => $tokenized,
            'leftover' => $leftover
        ));
        echo PHP_EOL;
        */
    }
    
    protected function removeTokens(string $statement) : string
    {
        $matches = array();
        preg_match_all('/'.sprintf($this->token, '[A-Z0-9_]+').'/sx', $statement, $matches, PREG_PATTERN_ORDER);
        
        foreach($matches[0] as $match)
        {
            $statement = str_replace($match, '', $statement);
        }
        
        return $statement;
    }
    
    protected $token = '__TOKEN_%s__';
    
    protected $tokens = array(
        'variables',
        'escaped_quotes',
        'operands',
        'string_literals',
        'numbers'
    );
    
    protected function tokenize(string $statement) : string
    {
        $tokenized = trim($statement);
        
        foreach($this->tokens as $token)
        {
            $method = 'tokenize_'.$token;
            
            if(!method_exists($this, $method))
            {
                throw new Mailcode_Exception(
                    'Unknown statement token.',    
                    sprintf(
                        'The tokenize method [%s] is not present in class [%s].',
                        $method,
                        get_class($this)
                    ),
                    self::ERROR_TOKENIZE_METHOD_MISSING
                );
            }
            
            $tokenized = $this->$method($tokenized);
        }
        
        return $tokenized;
    }
    
    protected function tokenize_escaped_quotes(string $tokenized) : string
    {
        return str_replace('\"', '__QUOTE__', $tokenized);
    }

    protected function tokenize_variables(string $tokenized) : string
    {
        $vars = Mailcode::create()->findVariables($tokenized)->getGroupedByHash();
        
        foreach($vars as $var)
        {
            $tokenized = str_replace($var->getMatchedText(), $this->getToken('VARIABLE'), $tokenized);
        }
        
        return $tokenized;
    }
    
    protected function tokenize_operands(string $tokenized) : string
    {
        foreach($this->operands as $operand)
        {
            $tokenized = str_replace($operand, $this->getToken('OPERAND'), $tokenized);
        }
        
        return $tokenized;
    }
    
    protected function tokenize_string_literals(string $tokenized) : string
    {
        $matches = array();
        preg_match_all('/"(.*)"/sx', $tokenized, $matches, PREG_PATTERN_ORDER);
        
        foreach($matches[0] as $match)
        {
            $tokenized = str_replace($match, $this->getToken('STRING_LITERAL'), $tokenized);
        }
        
        return $tokenized;
    }
    
    protected function tokenize_numbers(string $tokenized) : string
    {
        $matches = array();
        preg_match_all('/[0-9]+\s*[.,]\s*[0-9]+|[0-9]+/sx', $tokenized, $matches, PREG_PATTERN_ORDER);
        
        foreach($matches[0] as $match)
        {
            $tokenized = str_replace($match, $this->getToken('NUMBER'), $tokenized);
        }
        
        return $tokenized;
    }
    
    protected function getToken(string $name) : string
    {
        return sprintf($this->token, $name);
    }
}
