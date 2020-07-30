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
    
    public function getID() : string
    {
        $tokens = explode('_', get_class($this));
        
        return array_pop($tokens);
    }
    
    abstract protected function initFormatting(string $subject) : string;
    
    abstract public function getReplaceNeedle(Mailcode_Parser_Safeguard_Placeholder $placeholder) : string; 
    
    protected function resolveReplacementText(Mailcode_Parser_Safeguard_Placeholder_Locator_Location $location) : string
    {
        $class = sprintf('Mailcode\Mailcode_Parser_Safeguard_Formatter_%s_Location', $this->getID());
        
        $info = new $class($this, $location);
        
        return $info->getPlaceholderText();
    }
    
    public function format(string $subject) : string
    {
        $subject = $this->initFormatting($subject);
        
        $placeholders = $this->filterPlaceholders();
        
        $total = count($placeholders);
        
        for($i=0; $i < $total; $i++)
        {
            $subject = $this->process($placeholders[$i], $subject);
        }
        
        return $subject;
    }
    
    protected function process(Mailcode_Parser_Safeguard_Placeholder $placeholder, string $subject) : string
    {
        $locator = $placeholder->createLocator($subject);
        $positions = $locator->getLocations();
        
        foreach($positions as $position)
        {
            $replace = $this->resolveReplacementText($position);
            
            if($replace !== $position->getPlaceholderString())
            {
                $locator->replaceWith($position, $replace);
            }
        }
        
        return $locator->getSubjectString();
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
    * @return \Mailcode\Mailcode_Parser_Safeguard_Placeholder[]
    */
    protected function filterPlaceholders()
    {
        return $this->safeguard->getPlaceholders();
    }
}
