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

use function AppUtils\parseVariable;

/**
 * Command syntax formatters collection: handles the formatters
 * that will be used to format all commands in the safeguard's 
 * subject strings. 
 * 
 * By default, commands are formatted using the normalize formatter,
 * which ensures that all commands are normalized. Additional 
 * formatters can be added at will.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatting
{
    public const ERROR_UNKNOWN_FORMATTER = 65901;
    public const ERROR_TOO_MANY_REPLACERS = 65902;
    public const ERROR_NO_FORMATTERS_ADDED = 65903;
    
   /**
    * @var Mailcode_Parser_Safeguard
    */
    private $safeguard;
    
   /**
    * @var Mailcode_Parser_Safeguard_Formatter[]
    */
    private $formatters = array();
    
   /**
    * @var Mailcode_StringContainer
    */
    private $subject;
    
   /**
    * @var boolean
    */
    private $applied = false;
    
   /**
    * @var boolean
    */
    private $partial = false;
    
    public function __construct(Mailcode_Parser_Safeguard $safeguard, Mailcode_StringContainer $subject)
    {
        $this->safeguard = $safeguard;
        $this->subject = $subject;
    }
    
    public function getSubject() : Mailcode_StringContainer
    {
        return $this->subject;
    }
    
    public function getSafeguard() : Mailcode_Parser_Safeguard
    {
        return $this->safeguard;
    }
    
    public function addFormatter(Mailcode_Parser_Safeguard_Formatter $formatter) : void
    {
        $this->formatters[$formatter->getID()] = $formatter;
    }
    
    public function replaceWithNormalized() : Mailcode_Parser_Safeguard_Formatter_Type_Normalized
    {
        $formatter = $this->createNormalized();
        
        $this->addFormatter($formatter);
        
        return $formatter;
    }

    public function formatWithSingleLines() : Mailcode_Parser_Safeguard_Formatter_Type_SingleLines
    {
        $formatter = $this->createSingleLines();
        
        $this->addFormatter($formatter);
        
        return $formatter;
    }
    
   /**
    * Adds a formatter that will surround all variables with
    * markup to highlight them independently of command syntax
    * highlighting.
    * 
    * This is used to mark variables visually even after commands
    * have been replaced by the target system's post processing.
    * Can be combined with a replacer and other formats.
    * 
    * @return Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    */
    public function formatWithMarkedVariables() : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        $formatter = $this->createMarkVariables();
        
        $this->addFormatter($formatter);
        
        return $formatter;
    }
    
    public function replaceWithPlaceholders() : Mailcode_Parser_Safeguard_Formatter_Type_Placeholders
    {
        $formatter = $this->createPlaceholders();
        
        $this->addFormatter($formatter);
        
        return $formatter;
    }

    /**
     * Adds a formatter that removes all Mailcode commands.
     *
     * @return Mailcode_Parser_Safeguard_Formatter_Type_Remove
     */
    public function replaceWithRemovedCommands() : Mailcode_Parser_Safeguard_Formatter_Type_Remove
    {
        $formatter = $this->createRemoveCommands();

        $this->addFormatter($formatter);

        return $formatter;
    }
    
    public function replaceWithHTMLHighlighting() : Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    {
        $formatter = $this->createHTMLHighlighting();
        
        $this->addFormatter($formatter);
        
        return $formatter;
    }
    
    public function applyFormatting() : void
    {
        if($this->applied)
        {
            return;
        }
        
        $this->applied = true;
        
        $this->validateFormatters();
        
        $this->applyFormatTypes();
        $this->applyReplaceTypes();
    }

    private function applyFormatTypes() : void
    {
        foreach($this->formatters as $formatter)
        {
            if($formatter instanceof Mailcode_Parser_Safeguard_Formatter_FormatType)
            {
                $formatter->format();
            }
        }
    }
    
    private function applyReplaceTypes() : void
    {
        foreach($this->formatters as $formatter)
        {
            if($formatter instanceof Mailcode_Parser_Safeguard_Formatter_ReplacerType)
            {
                $formatter->replace();
            }
        }
    }
    
    private function validateFormatters() : void
    {
        if(empty($this->formatters))
        {
            throw new Mailcode_Exception(
                'No formatters selected',
                'At least one formatter needs to be added for the formatting to work.',
                self::ERROR_NO_FORMATTERS_ADDED
            );
        }
        
        $amount = $this->countReplacers();
        
        if($amount > 1) 
        {
            throw new Mailcode_Exception(
                'More than one replacer formatter selected',
                'A maximum of 1 replacer formatter may be selected.',
                self::ERROR_TOO_MANY_REPLACERS
            );
        }
        
        // by default, at minimum the normalized formatter must be selected.
        if($amount === 0)
        {
            $this->replaceWithNormalized();
        }
    }
    
   /**
    * Counts the amount of replacer formatters that have been added.
    * 
    * @return int
    */
    private function countReplacers() : int
    {
        $count = 0;
        
        foreach($this->formatters as $formatter)
        {
            if($formatter instanceof Mailcode_Parser_Safeguard_Formatter_ReplacerType)
            {
                $count++;
            }
        }
        
        return $count;
    }

   /**
    * Creates a formatter that adds HTML syntax highlighting
    * for all commands in the specified string, intelligently
    * checking the location of the commands to ensure that they
    * can be syntax highlighted.
    * 
    * For example, commands in HTML attributes will not be
    * highlighted, as this would break the HTML.
    *  
    * @return Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    */
    public function createHTMLHighlighting() : Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting($this);
    }

   /**
    * Creates the formatter that ensures that all commands
    * are placed on a separate line in the subject string.
    */
    public function createSingleLines() : Mailcode_Parser_Safeguard_Formatter_Type_SingleLines
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_SingleLines($this);
    }
    
   /**
    * Creates the formatter that replaces all commands by
    * their normalized variants.
    * 
    * @return Mailcode_Parser_Safeguard_Formatter_Type_Normalized
    */
    public function createNormalized() : Mailcode_Parser_Safeguard_Formatter_Type_Normalized
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_Normalized($this);
    }
    
    public function createPlaceholders() : Mailcode_Parser_Safeguard_Formatter_Type_Placeholders
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_Placeholders($this);
    }

    public function createRemoveCommands() : Mailcode_Parser_Safeguard_Formatter_Type_Remove
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_Remove($this);
    }

    public function createMarkVariables() : Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables($this);
    }

    public function createPreProcessing() : Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing
    {
        return new Mailcode_Parser_Safeguard_Formatter_Type_PreProcessing($this);
    }
    
    public function toString() : string
    {
        $this->applyFormatting();
        
        return $this->subject->getString();
    }

   /**
    * Whether the formatting is done partially: missing placeholders
    * will simply be ignored.
    * 
    * @return bool
    */
    public function isPartial() : bool
    {
        return $this->partial;
    }
    
   /**
    * The formatting will ignore missing placeholders. Use this if the
    * formatting will be done on a text that may not contain all of the
    * initial placeholders anymore.
    * 
    * This is like the safeguard's makeWholePartial() method.
    * 
    * @return Mailcode_Parser_Safeguard_Formatting
    */
    public function makePartial() : Mailcode_Parser_Safeguard_Formatting
    {
        $this->partial = true;
        
        return $this;
    }
}
