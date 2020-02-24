<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command safeguarder: used to replace the mailcode commands
 * in a string with placeholders, to allow safe text transformation
 * and filtering operations on strings, without risking to break
 * any of the contained commands (if any).
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Placeholder
{
    const ERROR_PLACEHOLDER_TOO_LONG = 47901;
    
   /**
    * @var int
    */
    protected $id;
    
   /**
    * @var Mailcode_Parser_Safeguard
    */
    protected $safeguard;

   /**
    * @var Mailcode_Commands_Command
    */
    protected $command;
    
   /**
    * @var string
    */
    protected $replacement = '';
    
    public function __construct(int $id, Mailcode_Commands_Command $command, Mailcode_Parser_Safeguard $safeguard)
    {
        $this->id = $id;
        $this->command = $command;
        $this->safeguard = $safeguard;
    }
    
    public function getID() : int
    {
        return $this->id;
    }
    
    public function getLength() : int
    {
        return mb_strlen($this->getOriginalText());
    }
    
    public function getReplacementText() : string
    {
        if(!empty($this->replacement))
        {
            return $this->replacement;
        }
        
        // prepend and append the delimiter characters
        $format = sprintf(
            '%1$s%2$s%1$s',
            $this->safeguard->getDelimiter(),
            '%s'
        );
        
        // the length of the placeholder, without the ID
        $length = strlen($format) - 2; // - 2 for the %s
        
        // to total amount of zeroes to pad with to obtain the total length
        $padLength = $this->getLength() - $length;
        
        if($padLength < 0) 
        {
            $padLength = 0;
        }
        
        // create the zero-padded ID to fill the format string with 
        $paddedID  = str_pad((string)$this->id, $padLength, '0');
        
        $this->replacement = sprintf($format, $paddedID);
        
        return $this->replacement;
    }
    
    public function getOriginalText() : string
    {
        return $this->command->getMatchedText();
    }
    
    public function getCommand() : Mailcode_Commands_Command
    {
        return $this->command;
    }
}
