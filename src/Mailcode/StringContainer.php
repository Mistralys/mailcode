<?php
/**
 * @package Mailcode
 * @subpackage Parser
 */

declare(strict_types=1);

namespace Mailcode;

use function AppUtils\parseVariable;

/**
 * Utility class made to hold a string, and notify listeners
 * whenever the string is modified.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_StringContainer
{
    public const ERROR_INVALID_CALLABLE = 65701;
    public const ERROR_UPDATE_CALLED_DURING_UPDATE_OPERATION = 65702;
    
   /**
    * @var string
    */
    private $subject;
    
   /**
    * @var integer
    */
    private static $listenerCounter = 0;
    
   /**
    * @var callable[]
    */
    private $listeners = array();
    
   /**
    * @var boolean
    */
    private $updating = false;
    
   /**
    * @var integer
    */
    private static $idCounter = 0;
    
   /**
    * @var integer
    */
    private $id;
    
   /**
    * @var integer
    */
    private $length;
    
    public function __construct(string $subject)
    {
        self::$idCounter++;
        
        $this->id = self::$idCounter;
        
        $this->updateString($subject);
    }
    
    public function getID() : int
    {
        return $this->id;
    }
    
   /**
    * Updates the string with the specified string.
    * Notifies all listeners of the change.
    * 
    * @param string $subject
    * @throws Mailcode_Exception
    * @return bool Whether the string had modifications.
    * 
    * @see Mailcode_StringContainer::ERROR_UPDATE_CALLED_DURING_UPDATE_OPERATION
    */
    public function updateString(string $subject) : bool
    {
        // avoid triggering an update if there are no changes in the string
        if($subject === $this->subject)
        {
            return false;
        }
        
        if($this->updating)
        {
            throw new Mailcode_Exception(
                'Cannot modify subject string during update',
                'Tried calling update() on a subject string during a running update, which is not allowed.',
                self::ERROR_UPDATE_CALLED_DURING_UPDATE_OPERATION
            );
        }
        
        $this->updating = true;
        
        $this->subject = $subject;
        $this->length = mb_strlen($this->subject);
        
        foreach($this->listeners as $listener)
        {
            $listener($this);
        }
        
        $this->updating = false;
        
        return true;
    }
    
   /**
    * Retrieves the stored string.
    * 
    * @return string
    */
    public function getString() : string
    {
        return $this->subject;
    }
    
   /**
    * Adds a listener that will be informed every time the string is modified.
    * The callback gets the string container instance as parameter.
    * 
    * @param callable|mixed $callback
    * @throws Mailcode_Exception If it is not a valid callable.
    * @return int The listener number, to be able to remove it using `removeListener()`.
    * 
    * @see Mailcode_StringContainer::removeListener()
    * @see Mailcode_StringContainer::ERROR_INVALID_CALLABLE
    */
    public function addListener($callback) : int
    {
        self::$listenerCounter++;
        
        if(!is_callable($callback))
        {
            throw new Mailcode_Exception(
                'Not a valid callable',
                sprintf(
                    'The specified callback parameter is not callable: [%s].',
                    parseVariable($callback)->enableType()->toString()
                ),
                self::ERROR_INVALID_CALLABLE
            );
        }
        
        $this->listeners[self::$listenerCounter] = $callback;
            
        return self::$listenerCounter;
    }
    
    public function getLength() : int
    {
        return $this->length;
    }
    
   /**
    * Removes an existing listener by its ID.
    * Has no effect if it does not exist, or has already been removed.
    * 
    * @param int $listenerID
    */
    public function removeListener(int $listenerID) : void
    {
        if(isset($this->listeners[$listenerID]))
        {
            unset($this->listeners[$listenerID]);
        }
    }
    
   /**
    * Replaces all substrings matching needle with the replacement text.
    *  
    * @param string $needle
    * @param string $replacement
    * @return bool
    */
    public function replaceSubstrings(string $needle, string $replacement) : bool
    {
        $string = str_replace($needle, $replacement, $this->subject);
        
        return $this->updateString($string);
    }

   /**
    * Get the position of a substring in the string.
    * 
    * @param string $needle
    * @return int|bool The zero-based position, or false if not found.
    */
    public function getSubstrPosition(string $needle)
    {
        return mb_strpos($this->subject, $needle);
    }
    
    public function getSubstr(int $start, ?int $length=null) : string
    {
        return mb_substr($this->subject, $start, $length);
    }
}
