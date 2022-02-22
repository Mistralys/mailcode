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
    public const ERROR_INVALID_COMMANDS = 47801;
    public const ERROR_PLACEHOLDER_NOT_FOUND = 47804;
    public const ERROR_NO_PLACEHOLDER_FOR_COMMAND = 47805;
    public const ERROR_NO_FIRST_PLACEHOLDER = 47806;

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
    * @var Mailcode_Parser_Safeguard_PlaceholderCollection|NULL
    */
    protected $placeholders;
    
   /**
    * @var string
    */
    protected $delimiter = '999';
    
    public function __construct(Mailcode_Parser $parser, string $subject)
    {
        $this->parser = $parser;
        $this->originalString = $subject;
    }

    /**
     * Resets the internal placeholders counter, which is
     * used to number the placeholder strings. Mainly used
     * in the tests suite.
     */
    public static function resetCounter() : void
    {
        self::$counter = 0;
    }
    
   /**
    * Retrieves the string the safeguard was created for.
    * 
    * @return string
    */
    public function getOriginalString() : string
    {
        return $this->originalString;
    }
    
   /**
    * Sets the delimiter character sequence used to prepend
    * and append to the placeholders.
    * 
    * The delimiter's default is "999".
    *
    * Minimum characters: 2
    * Invalid characters: Any characters that get URL encoded
    *
    * @param string $delimiter
    * @return Mailcode_Parser_Safeguard
    */
    public function setDelimiter(string $delimiter) : Mailcode_Parser_Safeguard
    {
        $validator = new Mailcode_Parser_Safeguard_DelimiterValidator($delimiter);
        $validator->throwExceptionIfInvalid();

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
        
        return $this->makeSafePartial();
    }
    
   /**
    * Like makeSafe(), but allows partial (invalid) commands: use this
    * if the subject string may contain only part of the whole set of
    * commands. 
    * 
    * Example: parsing a text with an opening if statement, without the 
    * matching end statement.
    * 
    * @return string
    */
    public function makeSafePartial() : string
    {
        $placeholders = $this->getPlaceholdersCollection()->getAll();
        $string = $this->originalString;
        
        foreach($placeholders as $placeholder)
        {
            $string = $this->makePlaceholderSafe($string, $placeholder);
        }

        $string = $this->protectContents($string);

        $this->analyzeURLs($string);

        return $string;
    }

    /**
     * Goes through all placeholders in the specified string, and
     * checks if there are any commands whose content must be protected,
     * like the `code` command.
     *
     * It automatically calls the protectContent method of the command,
     * which replaces the command with a separate placeholder text.
     *
     * @param string $string
     * @return string
     * @see Mailcode_Interfaces_Commands_ProtectedContent
     * @see Mailcode_Traits_Commands_ProtectedContent
     */
    private function protectContents(string $string) : string
    {
        $placeholders = $this->getPlaceholdersCollection()->getAll();

        foreach ($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();

            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                $closing = $command->getClosingCommand();
                $closingPlaceholder = $this->getPlaceholdersCollection()->getByCommand($closing);

                $string = $command->protectContent($string, $placeholder, $closingPlaceholder);
            }
        }

        return $string;
    }

    private function makePlaceholderSafe(string $string, Mailcode_Parser_Safeguard_Placeholder $placeholder) : string
    {
        $pos = mb_strpos($string, $placeholder->getOriginalText());

        if($pos === false)
        {
            throw new Mailcode_Exception(
                'Placeholder original text not found',
                sprintf(
                    'Tried finding the command string [%s], but it has disappeared.',
                    $placeholder->getOriginalText()
                ),
                self::ERROR_PLACEHOLDER_NOT_FOUND
            );
        }

        $before = mb_substr($string, 0, $pos);
        $after = mb_substr($string, $pos + mb_strlen($placeholder->getOriginalText()));

        return $before.$placeholder->getReplacementText().$after;
    }

    /**
     * Detects all URLs in the subject string, and tells all placeholders
     * that are contained in URLs, that they are in an URL.
     *
     * @param string $string
     */
    private function analyzeURLs(string $string) : void
    {
        $analyzer = new Mailcode_Parser_Safeguard_URLAnalyzer($string, $this);
        $analyzer->analyze();
    }
    
   /**
    * Creates a formatting handler, which can be used to specify
    * which formatting to use for the commands in the subject string.
    * 
    * @param Mailcode_StringContainer|string $subject
    * @return Mailcode_Parser_Safeguard_Formatting
    */
    public function createFormatting($subject) : Mailcode_Parser_Safeguard_Formatting
    {
        if(is_string($subject))
        {
            $subject = Mailcode::create()->createString($subject);
        }
        
        return new Mailcode_Parser_Safeguard_Formatting($this, $subject);
    }

    /**
     * Retrieves all placeholders that have to be added to
     * the subject text.
     *
     * @return Mailcode_Parser_Safeguard_Placeholder[]
     *
     * @deprecated Use the placeholder collection instead {@see Mailcode_Parser_Safeguard::getPlaceholdersCollection()}.
     */
    public function getPlaceholders() : array
    {
        return $this->getPlaceholdersCollection()->getAll();
    }

    /**
    * Retrieves all placeholders that have to be added to
    * the subject text.
    * 
    * @return Mailcode_Parser_Safeguard_PlaceholderCollection
    */
    public function getPlaceholdersCollection() : Mailcode_Parser_Safeguard_PlaceholderCollection
    {
        if(isset($this->placeholders))
        {
            return $this->placeholders;
        }
        
        $placeholders = array();
        $commands = $this->getCollection()->getCommands();
        
        foreach($commands as $command)
        {
            self::$counter++;
            
            $placeholders[] = new Mailcode_Parser_Safeguard_Placeholder(
                self::$counter,
                $command,
                $this
            );
        }

        $this->placeholders = new Mailcode_Parser_Safeguard_PlaceholderCollection($placeholders);

        return $this->placeholders;
    }

    /**
     * @param string $string
     * @param bool $partial
     * @param bool $highlighted
     * @return string
     * @throws Mailcode_Exception
     */
    protected function restore(string $string, bool $partial=false, bool $highlighted=false) : string
    {
        if(!$partial)
        {
            $this->requireValidCollection();
        }
        
        $formatting = $this->createFormatting($string);

        if($partial)
        {
            $formatting->makePartial();
        }
        
        if($highlighted)
        {
            $formatting->replaceWithHTMLHighlighting();
        }
        else 
        {
            $formatting->replaceWithNormalized();
        }
        
        return $this->restoreContents($formatting->toString());
    }

    private function restoreContents(string $string) : string
    {
        $placeholders = $this->getPlaceholdersCollection()->getAll();

        foreach ($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();

            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                $string = $command->restoreContent($string);
            }
        }

        return $string;
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
    */
    public function makeWhole(string $string) : string
    {
        return $this->restore(
            $string, 
            false, // partial? 
            false // highlight?
        );
    }
    
   /**
    * Like `makeWhole()`, but ignores missing command placeholders.
    *
    * @param string $string
    * @return string
    * @throws Mailcode_Exception
    *
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    */
    public function makeWholePartial(string $string) : string
    {
        return $this->restore(
            $string,
            true, // partial?
            false // highlight?
        );
    }

   /**
    * Like `makeWhole()`, but replaces the commands with a syntax
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
    */
    public function makeHighlighted(string $string) : string
    {
        return $this->restore(
            $string, 
            false, // partial? 
            true // highlighted?
        );
    }
    
   /**
    * Like `makeHighlighted()`, but ignores missing command placeholders.
    * 
    * @param string $string
    * @return string
    * @throws Mailcode_Exception
    *
    * @see Mailcode_Parser_Safeguard::ERROR_INVALID_COMMANDS
    */
    public function makeHighlightedPartial(string $string) : string
    {
        return $this->restore(
            $string, 
            true, // partial? 
            true // highlight?
        );
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
        
        $exception = new Mailcode_Exception(
            'Cannot safeguard invalid commands',
            sprintf(
                'The collection contains invalid commands. Safeguarding is only allowed with valid commands.'.
                'Source string: [%s]',
                $this->originalString
            ),
            self::ERROR_INVALID_COMMANDS
        );

        $exception->setCollection($this->getCollection());

        throw $exception;
    }
    
   /**
    * Retrieves a list of all placeholder IDs used in the text.
    * 
    * @return string[]
    *
    * @deprecated Use the placeholder collection instead {@see Mailcode_Parser_Safeguard::getPlaceholdersCollection()}.
    */
    public function getPlaceholderStrings() : array
    {
        return $this->getPlaceholdersCollection()->getStrings();
    }
    
    public function isPlaceholder(string $subject) : bool
    {
        return $this->getPlaceholdersCollection()->isStringPlaceholder($subject);
    }
    
   /**
    * Retrieves a placeholder instance by its ID.
    * 
    * @param int $id
    * @throws Mailcode_Exception If the placeholder was not found.
    * @return Mailcode_Parser_Safeguard_Placeholder
    *
    * @deprecated Use the placeholder collection instead {@see Mailcode_Parser_Safeguard::getPlaceholdersCollection()}.
    */
    public function getPlaceholderByID(int $id) : Mailcode_Parser_Safeguard_Placeholder
    {
        return $this->getPlaceholdersCollection()->getByID($id);
    }
    
   /**
    * Retrieves a placeholder instance by its replacement text.
    * 
    * @param string $string
    * @throws Mailcode_Exception
    * @return Mailcode_Parser_Safeguard_Placeholder
    *
    * @deprecated Use the placeholder collection instead {@see Mailcode_Parser_Safeguard::getPlaceholdersCollection()}.
    */
    public function getPlaceholderByString(string $string) : Mailcode_Parser_Safeguard_Placeholder
    {
        return $this->getPlaceholdersCollection()->getByString($string);
    }
    
    public function hasPlaceholders() : bool
    {
        return $this->getPlaceholdersCollection()->hasPlaceholders();
    }
}
