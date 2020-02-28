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
    protected $name;
    
   /**
    * @var string
    */
    protected $type;
    
   /**
    * @var string
    */
    protected $params;
    
   /**
    * @var string
    */
    protected $matchedString;
    
    public function __construct(string $name, string $type, string $params, string $matchedString)
    {
        $this->name = strtolower($name);
        $this->type = strtolower($type);
        $this->params = trim($params);
        $this->matchedString = $matchedString;
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
}
