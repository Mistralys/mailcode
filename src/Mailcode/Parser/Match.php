<?php
/**
 * File containing the {@see Mailcode_Parser} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

/**
 * Mailcode parser match, container for a command found
 * while parsing a string.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Match
{
   /**
    * @var string
    */
    protected string $name;
    
   /**
    * @var string
    */
    protected string $type;
    
   /**
    * @var string
    */
    protected string $params;
    
   /**
    * @var string
    */
    protected string $matchedString;
    
    public function __construct(string $name, string $type, string $params, string $matchedString)
    {
        $this->name = strtolower($name);
        $this->type = strtolower($type);
        $this->params = trim($params);
        $this->matchedString = $matchedString;
        
        $this->applyFilters();
    }
    
    public function getName() : string
    {
        return $this->name;
    }
    
    public function getType() : string
    {
        return $this->type;
    }
    
    public function getParams() : string
    {
        return $this->params;
    }
    
    public function getMatchedString() : string
    {
        return $this->matchedString;
    }
    
    private function applyFilters() : void
    {
        $this->params = $this->removeNonBreakingSpaces($this->params);
        $this->matchedString = $this->decodeBrackets($this->matchedString);
    }

    private function decodeBrackets(string $subject) : string
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
            $subject
        );
    }
    
    private function removeNonBreakingSpaces(string $subject) : string
    {
        return str_replace(array('&nbsp;', '&#160;'), ' ', $subject);
    }
}
