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
    protected $eol;
    
   /**
    * @var int
    */
    protected $eolLength;
    
    public function format(string $subject) : string
    {
        $placeholders = $this->resolvePlaceholderStrings();
        
        $this->eol = $this->resolveNewlineChar($subject);
        $this->eolLength = strlen($this->eol);
        
        $total = count($placeholders);
        
        for($i=0; $i < $total; $i++)
        {
            $subject = $this->process($placeholders[$i], $subject);
        }
        
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

    protected function process(string $placeholder, string $subject) : string
    {
        $positions = $this->resolvePositions($placeholder, $subject);
        $phLength = mb_strlen($placeholder);
        $offset = 0;
        
        foreach($positions as $position)
        {
            // adjust the position if previous changes made the subject longer
            $position += $offset;
            
            $info = new Mailcode_Parser_Safeguard_Formatter_SingleLines_Placeholder(
                $this, 
                $subject, 
                $phLength, 
                $position
            );
            
            if(!$info->requiresAdjustment())
            {
                continue;
            }
            
            $adjusted = $this->resolveAdjustedPlaceholder($info, $placeholder);
            
            // cut the subject string so we can insert the adjusted placeholder
            $start = mb_substr($subject, 0, $position);
            $end = mb_substr($subject, $position + $phLength);
            
            // rebuild the subject string from the parts
            $subject = $start.$adjusted.$end;
            
            // the placeholder length has changed, which means subsequent
            // positions have to be increased by the added length, which
            // we do using the offset.
            $offset += mb_strlen($adjusted) - $phLength; 
        }
        
        return $subject;
    }
    
    protected function resolveAdjustedPlaceholder(Mailcode_Parser_Safeguard_Formatter_SingleLines_Placeholder $info, string $placeholder) : string
    {
        $prepend = '';
        $append = '';
        
        if($info->requiresPrepend())
        {
            $prepend = $this->eol;
        }
        
        if($info->requiresAppend())
        {
            $append = $this->eol;
        }
        
        return $prepend.$placeholder.$append;
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
}

