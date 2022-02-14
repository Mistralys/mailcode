<?php
/**
 * File containing the {@see Mailcode_Date_FormatInfo} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Date_FormatInfo
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\OperationResult;

/**
 * Main hub for information all around the date format strings
 * that can be used in the ShowDate command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Date_FormatInfo
{
    public const VALIDATION_INVALID_FORMAT_CHARACTER = 55801;
    public const VALIDATION_EMPTY_FORMAT_STRING = 55802;
    
    public const CHARTYPE_DATE = 'date';
    public const CHARTYPE_TIME = 'time';
    public const CHARTYPE_PUNCTUATION = 'punctuation';
    
   /**
    * @var string
    */
    private $defaultFormat = "Y/m/d";
    
   /**
    * @var Mailcode_Date_FormatInfo_Character[]
    */
    private $formatChars = array();
    
   /**
    * @var string[]
    */
    private $allowedChars = array();
    
   /**
    * @var Mailcode_Date_FormatInfo|NULL
    */
    private static $instance;
    
    private function __construct()
    {
        $this->initCharacters();
    }
    
    public static function getInstance() : Mailcode_Date_FormatInfo
    {
        if(!isset(self::$instance))
        {
            self::$instance = new Mailcode_Date_FormatInfo();
        }
        
        return self::$instance;
    }

   /**
    * Initialized the list of allowed date formatting
    * characters. This is done only once per request
    * by storing them statically for performance reasons.
    */
    private function initCharacters() : void
    {
        $chars = array(
            array(self::CHARTYPE_DATE, 'd', t('Day of the month, with leading zeros')),
            array(self::CHARTYPE_DATE, 'j', t('Day of the month, without leading zeros')),
            array(self::CHARTYPE_DATE, 'm', t('Month number, with leading zeros')),
            array(self::CHARTYPE_DATE, 'n', t('Month number, without leading zeros')),
            array(self::CHARTYPE_DATE, 'Y', t('Year, 4 digits')),
            array(self::CHARTYPE_DATE, 'y', t('Year, 2 digits')),
            
            array(self::CHARTYPE_TIME, 'H', t('Hour, 24-hour format with leading zeros')),
            array(self::CHARTYPE_TIME, 'i', t('Minutes with leading zeros')),
            array(self::CHARTYPE_TIME, 's', t('Seconds with leading zeros')),
            
            array(self::CHARTYPE_PUNCTUATION, '.', t('Dot')),
            array(self::CHARTYPE_PUNCTUATION, '/', t('Slash')),
            array(self::CHARTYPE_PUNCTUATION, '-', t('Hyphen')),
            array(self::CHARTYPE_PUNCTUATION, ':', t('Colon')),
            array(self::CHARTYPE_PUNCTUATION, ' ', t('Space'))
        );
        
        foreach($chars as $def)
        {
            $char = new Mailcode_Date_FormatInfo_Character(
                $def[0],
                $def[1],
                $def[2]
            );
            
            $this->formatChars[] = $char;
            $this->allowedChars[] = $char->getChar();
        }
    }
    
    public function getDefaultFormat() : string
    {
        return $this->defaultFormat;
    }
    
    public function setDefaultFormat(string $formatString) : void
    {
        $this->defaultFormat = $formatString;
    }
    
   /**
    * Validates the date format string, by ensuring that
    * all the characters it is composed of are known.
    *
    * @param string $formatString
    * @return OperationResult
    * 
    * @see Mailcode_Commands_Command_ShowDate::VALIDATION_EMPTY_FORMAT_STRING
    * @see Mailcode_Commands_Command_ShowDate::VALIDATION_INVALID_FORMAT_CHARACTER
    */
    public function validateFormat(string $formatString) : OperationResult
    {
        $result = new OperationResult($this);
        
        $trimmed = trim($formatString);
        
        if(empty($trimmed))
        {
            $result->makeError(
                t('Empty date format.'),
                self::VALIDATION_EMPTY_FORMAT_STRING
            );
            
            return $result;
        }
        
        $chars = ConvertHelper::string2array($formatString);
        $total = count($chars);
        
        for($i=0; $i < $total; $i++)
        {
            $char = $chars[$i];
            
            if(!in_array($char, $this->allowedChars))
            {
                $result->makeError(
                    t('Invalid character in date format:').' '.
                    t('%1$s at position %2$s.', '<code>'.$char.'</code>', $i+1),
                    self::VALIDATION_INVALID_FORMAT_CHARACTER
                );
                
                return $result;
            }
        }
        
        return $result;
    }
    
   /**
    * Retrieves all characters that are allowed to
    * be used in a date format string, with information
    * on each.
    *
    * @return Mailcode_Date_FormatInfo_Character[]
    */
    public function getCharactersList() : array
    {
        return $this->formatChars;
    }
    
   /**
    * Retrieves the characters list, grouped by type label.
    * 
    * @return array<string, array>
    */
    public function getCharactersGrouped() : array
    {
        $grouped = array();
        
        foreach($this->formatChars as $char)
        {
            $type = $char->getTypeLabel();
            
            if(!isset($grouped[$type]))
            {
                $grouped[$type] = array();
            }
            
            $grouped[$type][] = $char;
        }

        $groups = array_keys($grouped);
        
        foreach($groups as $group)
        {
            usort($grouped[$group], function(Mailcode_Date_FormatInfo_Character $a, Mailcode_Date_FormatInfo_Character $b)
            {
                return strnatcasecmp($a->getChar(), $b->getChar());
            });
        }
        
        uksort($grouped, function(string $a, string $b) 
        {
            return strnatcasecmp($a, $b);
        });
        
        return $grouped;
    }
}
