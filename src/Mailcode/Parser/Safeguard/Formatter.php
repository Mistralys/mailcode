<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Abstract base class for safeguard formatters: these 
 * are used to apply diverse formattings to the string
 * being parsed.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Safeguard_Formatter
{
   /**
    * @var Mailcode_Parser_Safeguard
    */
    protected $safeguard;
    
    public function __construct(Mailcode_Parser_Safeguard $safeguard)
    {
        $this->safeguard = $safeguard;
    }
    
    abstract public function format(string $subject) : string;
    
   /**
    * Resolves a list of positions of needle in the haystack.
    * 
    * @param string $needle
    * @param string $haystack
    * @return int[]
    */
    protected function resolvePositions(string $needle, string $haystack) : array
    {
        $lastPos = 0;
        $positions = array();
        
        while (($lastPos = mb_strpos($haystack, $needle, $lastPos))!== false)
        {
            $positions[] = $lastPos;
            $lastPos = $lastPos + mb_strlen($needle);
        }
        
        return $positions;
    }
    
   /**
    * Resolves the newline character used in the string.
    * 
    * @param string $subject
    * @return string
    */
    protected function resolveNewlineChar(string $subject) : string
    {
        $eol = ConvertHelper::detectEOLCharacter($subject);
        
        if($eol)
        {
            return $eol->getCharacter();
        }
        
        return PHP_EOL;
    }
    
   /**
    * Resolves the list of placeholder strings that need
    * to be formatted. This includes only commands that
    * do not generate content.
    *  
    * @return string[]
    */
    protected function resolvePlaceholderStrings() : array
    {
        $placeholders = $this->filterPlaceholders();
        
        $result = array();
        
        foreach($placeholders as $placeholder)
        {
            $result[] = $placeholder->getReplacementText();
        }
        
        return $result;
    }
    
   /**
    * @return \Mailcode\Mailcode_Parser_Safeguard_Placeholder[]
    */
    protected function filterPlaceholders()
    {
        return $this->safeguard->getPlaceholders();
    }
}
