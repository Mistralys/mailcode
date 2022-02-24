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
use function AppUtils\parseVariable;

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
    public const ERROR_INVALID_LOCATION_INSTANCE = 65601;
    
   /**
    * @var Mailcode_Parser_Safeguard_Formatting
    */
    protected $formatting;
    
   /**
    * @var Mailcode_StringContainer
    */
    protected $subject;
    
   /**
    * @var string[]
    */
    protected $log = array();
    
    public function __construct(Mailcode_Parser_Safeguard_Formatting $formatting)
    {
        $this->formatting = $formatting;
        $this->subject = $formatting->getSubject();
        
        $this->initFormatting();
    }
    
    public function getID() : string
    {
        $tokens = explode('_', get_class($this));
        
        return array_pop($tokens);
    }
    
    public function getFormatting() : Mailcode_Parser_Safeguard_Formatting
    {
        return $this->formatting;
    }
    
    public function getSubject() : Mailcode_StringContainer
    {
        return $this->subject;
    }
    
    public function getSafeguard() : Mailcode_Parser_Safeguard
    {
        return $this->formatting->getSafeguard();
    }
    
    abstract public function getPriority() : int;
    
    abstract protected function initFormatting() : void;
    
    protected function createLocation(Mailcode_Parser_Safeguard_Placeholder $placeholder) : Mailcode_Parser_Safeguard_Formatter_Location
    {
        $class = sprintf('Mailcode\Mailcode_Parser_Safeguard_Formatter_Type_%s_Location', $this->getID());
        
        $instance = new $class($this, $placeholder);
        
        if($instance instanceof Mailcode_Parser_Safeguard_Formatter_Location)
        {
            return $instance;
        }
        
        throw new Mailcode_Exception(
            'Invalid location instance created.',
            sprintf(
                'Expected a class of type [%s], got [%s].',
                Mailcode_Parser_Safeguard_Formatter_Location::class,
                parseVariable($instance)->enableType()->toString()
            ),
            self::ERROR_INVALID_LOCATION_INSTANCE
        );
    }
    
   /**
    * Retrieves all formatter-specific placeholder locations 
    * in the subject string.
    * 
    * @return Mailcode_Parser_Safeguard_Formatter_Location[]
    */
    protected function resolveLocations() : array
    {
        $placeholders = $this->formatting->getSafeguard()->getPlaceholdersCollection()->getAll();
        
        $result = array();
        
        foreach($placeholders as $placeholder)
        {
            $result[] = $this->createLocation($placeholder);
        }
        
        return $result;
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
            $this->log(sprintf(
                'Detected EOL character: %s.', 
                ConvertHelper::hidden2visible($eol->getCharacter())
            ));
            
            return $eol->getCharacter();
        }
        
        $this->log(sprintf(
            'Could not detect EOL character, using default: %s.', 
            ConvertHelper::hidden2visible(PHP_EOL)
        ));
        
        return PHP_EOL;
    }
    
    protected function log(string $message) : void
    {
        $this->log[] = sprintf(
            '%s Formatter | %s',
            $this->getID(),
            $message
        );
    }
 
   /**
    * @return string[]
    */
    public function getLog() : array
    {
        return $this->log;
    }
}
