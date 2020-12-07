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
    
    public function getReplacementLength() : int
    {
        return strlen($this->getReplacementText());
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
        $length = strlen($format) - 2; // -2 for the %s
        
        // to total amount of zeroes to pad with to obtain the total length
        $padLength = $this->getLength() - $length;
        
        if($padLength < 0) 
        {
            $padLength = 0;
        }
        
        // Create the padded ID to fill the format string with.
        //
        // Why add the *? To avoid ambiguity with the placeholder's
        // ID with 1-based numbers (1, 10, 100...). Since we pad to the
        // command's length, we would break the ID when using zeroes.
        //
        // For example: Imagine two commands with a length of 12 characters.
        // The first command has the ID "1", the other the ID "10". They
        // would both get this placeholder: "100000000000". Using hyphens
        // as padding character, there is no ambiguity. It becomes
        // "1------------" and "10-----------", keeping the ID intact.

        $paddedID  = str_pad((string)$this->id, $padLength, '-');
        
        $this->replacement = sprintf($format, $paddedID);

        return $this->replacement;
    }

    public function getOriginalText() : string
    {
        return $this->command->getMatchedText();
    }
    
    public function getNormalizedText() : string
    {
        return $this->command->getNormalized();
    }
    
    public function getHighlightedText() : string
    {
        return $this->command->getHighlighted();
    }
    
    public function getCommand() : Mailcode_Commands_Command
    {
        return $this->command;
    }
    
   /**
    * Serializes the placeholder's information into 
    * an array with the following keys:
    * 
    *   - originalText
    *   - replacementText
    *   - normalizedText
    *   - length
    *   - id
    * 
    * @return array<string,string|integer>
    */
    public function serialize() : array
    {
        return array(
            'originalText' => $this->getOriginalText(),
            'replacementText' => $this->getReplacementText(),
            'normalizedText' => $this->getNormalizedText(),
            'length' => $this->getLength(),
            'id' => $this->getID()
        );
    }
}
