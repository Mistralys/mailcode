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
 * Usage:
 * 
 * <pre>
 * $safeguard = Mailcode::create()->createSafeguard($sourceString);
 * 
 * // replace all commands with placeholders
 * $workString = $safeguard->makeSafe();
 * 
 * // dome something with the work string - filtering, parsing...
 * 
 * // restore all command placeholders
 * $resultString = $safeguard->makeWhole($workString);
 * </pre>
 * 
 * Note that by default, the placeholders are delimited with
 * two underscores, e.g. <code>__PCH0001__</code>. If the text
 * transformations include replacing or modifying underscores,
 * you should use a different delimiter:
 * 
 * <pre>
 * $safeguard = Mailcode::create()->createSafeguard($sourceString);
 * 
 * // change the delimiter to %%. Can be any arbitrary string.
 * $safeguard->setDelimiter('%%');
 * </pre>
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard
{
    const ERROR_INVALID_COMMANDS = 47801;
    
    const ERROR_COMMAND_PLACEHOLDER_MISSING = 47802;
    
    const ERROR_EMPTY_DELIMITER = 47803;
    
    const ERROR_PLACEHOLDER_NOT_FOUND = 47804;
    
   /**
    * @var Mailcode_Parser
    */
    protected $parser;
    
   /**
    * @var Mailcode_Collection
    */
    protected $commands;
    
   /**
    * @var string
    */
    protected $originalString;
    
   /**
    * @var Mailcode_Collection
    */
    protected $collection;
    
   /**
    * Counter for the placeholders, global for all placeholders.
    * @var integer
    */
    private static $counter = 0;
    
   /**
    * @var Mailcode_Parser_Safeguard_Placeholder[]
    */
    protected $placeholders;
    
   /**
    * @var string
    */
    protected $delimiter = '__';
    
   /**
    * @var string[]|NULL
    */
    protected $placeholderStrings;
    
    public function __construct(Mailcode_Parser $parser, string $subject)
    {
        $this->parser = $parser;
        $this->originalString = $subject;
    }
    
   /**
    * Sets the delimiter character sequence used to prepend
    * and append to the placeholders.
    * 
    * The delimiter's default is "__" (two underscores).
    * 
    * @param string $delimiter
    * @return Mailcode_Parser_Safeguard
    */
    public function setDelimiter(string $delimiter) : Mailcode_Parser_Safeguard
    {
        if(empty($delimiter))
        {
            throw new Mailcode_Exception(
                'Empty delimiter',
                'Delimiters may not be empty.',
                self::ERROR_EMPTY_DELIMITER
            );
        }
        
        $this->delimiter = $delimiter;
        
        return $this;
    }
    
    public function getDelimiter() : string
    {
        return $this->delimiter;
    }
    
   /**
    * Retrieves the safe string in which all commands have been replaced
    * by placeholder strings.
    * 
    * @return string
    * @throws Mailcode_Exception 
    *
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    */
    public function makeSafe() : string
    {
        $this->requireValidCollection();
        
        $replaces = $this->getReplaces();
                
        return str_replace(array_values($replaces), array_keys($replaces), $this->originalString);
    }
    
   /**
    * @param bool $highlighted
    * @return string[]string
    */
    protected function getReplaces(bool $highlighted=false) : array
    {
        $placeholders = $this->getPlaceholders();
        
        $replaces = array();
        
        foreach($placeholders as $placeholder)
        {
            $replace = '';
            
            if($highlighted)
            {
                $replace = $placeholder->getHighlightedText();
            }
            else 
            {
                $replace = $placeholder->getOriginalText();
            }
            
            $replaces[$placeholder->getReplacementText()] = $replace;
        }
        
        return $replaces;
    }
    
    
   /**
    * Retrieves all placeholders that have to be added to
    * the subject text.
    * 
    * @return \Mailcode\Mailcode_Parser_Safeguard_Placeholder[]
    */
    public function getPlaceholders()
    {
        if(isset($this->placeholders))
        {
            return $this->placeholders;
        }
        
        $this->placeholders = array();
        
        $cmds = $this->getCollection()->getGroupedByHash();
        
        foreach($cmds as $command)
        {
            self::$counter++;
            
            $this->placeholders[] = new Mailcode_Parser_Safeguard_Placeholder(
                self::$counter,
                $command,
                $this
            );
        }

        return $this->placeholders;
    }
    
    protected function restore(string $string, bool $highlighted=false) : string
    {
        $this->requireValidCollection();
        
        $replaces = $this->getReplaces($highlighted);
        
        $placeholderStrings = array_keys($replaces);
        
        foreach($placeholderStrings as $search)
        {
            if(!strstr($string, $search))
            {
                throw new Mailcode_Exception(
                    'Command placeholder not found',
                    sprintf(
                        'A placeholder for a command could not be found in the string to restore: [%s].',
                        $search
                    ),
                    self::ERROR_COMMAND_PLACEHOLDER_MISSING
                );
            }
        }
        
        return str_replace($placeholderStrings, array_values($replaces), $string);
    }
    
   /**
    * Makes the string whole again after transforming or filtering it,
    * by replacing the command placeholders with the original commands.
    *
    * @param string $string
    * @return string
    * @throws Mailcode_Exception
    *
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    * @see Mailcode_Parser_Safeguard::ERROR_COMMAND_PLACEHOLDER_MISSING
    */
    public function makeWhole(string $string) : string
    {
        return $this->restore($string, false);
    }

   /**
    * Like makeWhole(), but replaces the commands with a syntax
    * highlighted version, meant for human readable texts only.
    * 
    * Note: the commands lose their functionality (They cannot be 
    * parsed from that string again).
    *
    * @param string $string
    * @return string
    * @throws Mailcode_Exception
    *
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    * @see Mailcode_Parser_Safeguard::ERROR_COMMAND_PLACEHOLDER_MISSING
    */
    public function makeHighlighted(string $string) : string
    {
        return $this->restore($string, true);
    }
    
   /**
    * Retrieves the commands collection contained in the string.
    * 
    * @return Mailcode_Collection
    */
    public function getCollection() : Mailcode_Collection
    {
        if(isset($this->collection))
        {
            return $this->collection;
        }
        
        $this->collection = $this->parser->parseString($this->originalString);
        
        return $this->collection;
    }
    
    public function isValid() : bool
    {
        return $this->getCollection()->isValid();
    }
    
   /**
    * @throws Mailcode_Exception
    * 
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    */
    protected function requireValidCollection() : void
    {
        if($this->getCollection()->isValid())
        {
            return;
        }
        
        throw new Mailcode_Exception(
            'Cannot safeguard invalid commands',
            sprintf(
                'The collection contains invalid commands. Safeguarding is only allowed with valid commands. Source string:<br>'.
                $this->originalString
            ),
            self::ERROR_INVALID_COMMANDS
        );
    }
    
   /**
    * Retrieves a list of all placeholder IDs used in the text.
    * 
    * @return string[]
    */
    public function getPlaceholderStrings() : array
    {
        if(isset($this->placeholderStrings))
        {
            return $this->placeholderStrings;
        }
        
        $placeholders = $this->getPlaceholders();
        
        $this->placeholderStrings = array();
        
        foreach($placeholders as $placeholder)
        {
            $this->placeholderStrings[] = $placeholder->getReplacementText();
        }
        
        return $this->placeholderStrings;
    }
    
    public function isPlaceholder(string $subject) : bool
    {
        $ids = $this->getPlaceholderStrings();
        
        return in_array($subject, $ids);
    }
    
   /**
    * Retrieves a placeholder instance by its ID.
    * 
    * @param int $id
    * @throws Mailcode_Exception If the placeholder was not found.
    * @return Mailcode_Parser_Safeguard_Placeholder
    */
    public function getPlaceholderByID(int $id) : Mailcode_Parser_Safeguard_Placeholder
    {
        $placeholders = $this->getPlaceholders();
        
        foreach($placeholders as $placeholder)
        {
            if($placeholder->getID() === $id)
            {
                return $placeholder;
            }
        }
        
        throw new Mailcode_Exception(
            'No such safeguard placeholder.',
            sprintf(
                'The placeholder ID [%s] is not present in the safeguard instance.',
                $id
            ),
            self::ERROR_PLACEHOLDER_NOT_FOUND
        );
    }
    
   /**
    * Retrieves a placeholder instance by its replacement text.
    * 
    * @param string $string
    * @throws Mailcode_Exception
    * @return Mailcode_Parser_Safeguard_Placeholder
    */
    public function getPlaceholderByString(string $string) : Mailcode_Parser_Safeguard_Placeholder
    {
        $placeholders = $this->getPlaceholders();
        
        foreach($placeholders as $placeholder)
        {
            if($placeholder->getReplacementText() === $string)
            {
                return $placeholder;
            }
        }
        
        throw new Mailcode_Exception(
            'No such safeguard placeholder.',
            sprintf(
                'The placeholder replacement string [%s] is not present in the safeguard instance.',
                $string
            ),
            self::ERROR_PLACEHOLDER_NOT_FOUND
        );
    }
    
    public function hasPlaceholders() : bool
    {
        $placeholders = $this->getPlaceholders();
        
        return !empty($placeholders);
    }
}
