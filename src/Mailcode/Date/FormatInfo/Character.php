<?php
/**
 * File containing the {@see Mailcode_Date_FormatInfo_Character} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Date_FormatInfo_Character
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Stores information on a single date format character
 * that can be used in the ShowDate command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Date_FormatInfo_Character
{
    const ERROR_UNHANDLED_CHARTYPE = 55601;
    
   /**
    * @var string
    */
    private $type;
    
   /**
    * @var string
    */
    private $char;
    
   /**
    * @var string
    */
    private $description;
    
    public function __construct(string $type, string $char, string $description)
    {
        $this->type = $type;
        $this->char = $char;
        $this->description = $description;
    }
    
   /**
    * Retrieves the format character (PHP date format).
    * 
    * @return string 
    */
    public function getChar() : string
    {
        return $this->char;
    }
    
   /**
    * Retrieves a human readable description of the character's role.
    * 
    * @return string
    */
    public function getDescription() : string
    {
        return $this->description;
    }
    
   /**
    * Retrieves the character type ID.
    * 
    * @return string
    * 
    * @see Mailcode_Date_FormatInfo::CHARTYPE_DATE
    * @see Mailcode_Date_FormatInfo::CHARTYPE_TIME
    * @see Mailcode_Date_FormatInfo::CHARTYPE_PUNCTUATION
    */
    public function getTypeID() : string
    {
        return $this->type;
    }
    
   /**
    * Retrieves a human readable label for the character's type, e.g. "Date", "Time", "Punctuation".
    * 
    * @throws Mailcode_Exception If the character type is unknown.
    * @return string
    */
    public function getTypeLabel() : string
    {
        switch($this->type)
        {
            case Mailcode_Date_FormatInfo::CHARTYPE_DATE:
                return t('Date');
                
            case Mailcode_Date_FormatInfo::CHARTYPE_TIME:
                return t('Time');
            
            case Mailcode_Date_FormatInfo::CHARTYPE_PUNCTUATION:
                return t('Punctuation');
        }
        
        throw new Mailcode_Exception(
            'Unhandled date character type',
            sprintf(
                'The date character type [%s] is not known.',
                $this->type
            ),
            self::ERROR_UNHANDLED_CHARTYPE
        );
    }
}
