<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowDate} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowDate
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Translates the "ShowDate" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowDate extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowDate
{
    const ERROR_UNKNOWN_DATE_FORMAT_CHARACTER = 55501;
    
   /**
    * @var string[]string
    */
    private $charTable = array(
        'd' => 'd',
        'm' => 'M',
        'Y' => 'yyyy',
        'y' => 'yy',
        'H' => 'H',
        'i' => 'm',
        's' => 's',
        '.' => '.',
        ':' => ':',
        '-' => '-',
        '/' => '/',
        ' ' => ' '
    );
    
    public function translate(Mailcode_Commands_Command_ShowDate $command): string
    {
        return sprintf(
            '$date.format("%s", $%s)',
            $this->translateFormat($command->getFormatString()),
            ltrim($command->getVariableName(), '$')
        );
    }
    
    private function translateFormat(string $formatString) : string
    {
        $chars = ConvertHelper::string2array($formatString);
        $result = array();
        
        foreach($chars as $char)
        {
            if(!isset($this->charTable[$char]))
            {
                throw new Mailcode_Translator_Exception(
                    'Unknown date format string character',
                    sprintf(
                        'No translation for character %s available.',
                        ConvertHelper::hidden2visible($char)
                    ),
                    self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER
                );
                
            }
            
            $result[] = $this->charTable[$char]; 
        }
        
        return implode('', $result);
    }
}
