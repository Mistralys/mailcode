<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_SingleLines} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_SingleLines
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Single line formatter: ensures that all commands in the
 * subject string are placed on a separate line. This is 
 * typically used when using a custom parser for HTML documents,
 * to make it easier to identify commands.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_SingleLines extends Mailcode_Parser_Safeguard_Formatter
{
   /**
    * @var string
    */
    private $eol;
    
   /**
    * @var int
    */
    private $eolLength;
    
    protected function initFormatting(string $subject) : string
    {
        $this->eol = $this->resolveNewlineChar($subject);
        $this->eolLength = strlen($this->eol);
        
        return $subject;
    }
    
    public function getEOLChar() : string
    {
        return $this->eol;
    }
    
    public function getEOLLength() : int
    {
        return $this->eolLength;
    }

   /**
    * We only use placeholders that contain commands that do
    * not generate contents, since these are the only ones
    * that may need adjusting.
    * 
    * @return \Mailcode\Mailcode_Parser_Safeguard_Placeholder[]
    */
    protected function filterPlaceholders()
    {
        $placeholders = $this->safeguard->getPlaceholders();
        
        $result = array();
        
        foreach($placeholders as $placeholder)
        {
            if(!$placeholder->getCommand()->generatesContent())
            {
                $result[] = $placeholder;
            }
        }
        
        return $result;
    }
    
    public function getReplaceNeedle(Mailcode_Parser_Safeguard_Placeholder $placeholder) : string
    {
        return $placeholder->getReplacementText();
    }
}

