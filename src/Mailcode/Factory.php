<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory
{
    const ERROR_INVALID_COMMAND_CREATED = 50001;
    const ERROR_UNEXPECTED_COMMAND_TYPE = 50002;

   /**
    * @var Mailcode_Factory_CommandSets
    */
    private static $commandSets;
    
   /**
    * Creates a ShowVariable command.
    * 
    * @param string $variableName A variable name, with or without the $ sign prepended.
    * @return Mailcode_Commands_Command_ShowVariable
    */
    public static function showVar(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        return self::$commandSets->show()->showVar($variableName);
    }
    
    /**
     * Creates a ShowDate command, used to display date variables and 
     * format the date using date format strings.
     *
     * @param string $variableName A variable name, with or without the $ sign prepended.
     * @param string $formatString A date format string, or empty string for default.
     * @return Mailcode_Commands_Command_ShowDate
     */
    public static function showDate(string $variableName, string $formatString="") : Mailcode_Commands_Command_ShowDate
    {
        return self::$commandSets->show()->showDate($variableName, $formatString);
    }

   /**
    * Creates a ShowSnippet command.
    *
    * @param string $snippetName A snippet name, with or without the $ sign prepended.
    * @return Mailcode_Commands_Command_ShowSnippet
    */
    public static function showSnippet(string $snippetName) : Mailcode_Commands_Command_ShowSnippet
    {
        return self::$commandSets->show()->showSnippet($snippetName);
    }
    
   /**
    * Creates a SetVariable command.
    * 
    * @param string $variableName A variable name, with or without the $ sign prepended.
    * @param string $value
    * @param bool $quoteValue Whether to treat the value as a string literal, and add quotes to it.
    * @return Mailcode_Commands_Command_SetVariable
    * @throws Mailcode_Factory_Exception
    * 
    * @see Mailcode_Factory::ERROR_INVALID_COMMAND_CREATED
    */
    public static function setVar(string $variableName, string $value, bool $quoteValue=true) : Mailcode_Commands_Command_SetVariable
    {
        return self::$commandSets->set()->setVar($variableName, $value, $quoteValue);
    }
    
   /**
    * Like setVar(), but treats the value as a string literal
    * and automatically adds quotes to it.
    * 
    * @param string $variableName
    * @param string $value
    * @return Mailcode_Commands_Command_SetVariable
    */
    public static function setVarString(string $variableName, string $value) : Mailcode_Commands_Command_SetVariable
    {
        return self::$commandSets->set()->setVar($variableName, $value, true);
    }
    
    public static function comment(string $comments) : Mailcode_Commands_Command_Comment
    {
        return self::$commandSets->misc()->comment($comments);
    }
    
    public static function else() : Mailcode_Commands_Command_Else
    {
        return self::$commandSets->if()->else();
    }
    
    public static function end() : Mailcode_Commands_Command_End
    {
        return self::$commandSets->if()->end();
    }
    
    public static function if(string $condition, string $type='') : Mailcode_Commands_Command_If
    {
        return self::$commandSets->if()->if($condition, $type);
    }
    
    public static function ifVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        return self::$commandSets->if()->ifVar($variable, $operand, $value, $quoteValue);
    }

    public static function ifVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_If_Variable
    {
        return self::$commandSets->if()->ifVarString($variable, $operand, $value);
    }
    
    public static function ifVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        return self::$commandSets->if()->ifVarEquals($variable, $value, $quoteValue);
    }

    public static function ifVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If
    {
        return self::$commandSets->if()->ifVarEqualsString($variable, $value);
    }
    
    public static function ifVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        return self::$commandSets->if()->ifVarNotEquals($variable, $value, $quoteValue);
    }

    public static function ifVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If_Variable
    {
        return self::$commandSets->if()->ifVarNotEqualsString($variable, $value);
    }
    
    public static function elseIf(string $condition, string $type='') : Mailcode_Commands_Command_ElseIf
    {
        return self::$commandSets->elseIf()->elseIf($condition, $type);
    }
    
    public static function elseIfEmpty(string $variable) : Mailcode_Commands_Command_ElseIf_Empty
    {
        return self::$commandSets->elseIf()->elseIfEmpty($variable);
    }
    
    public static function elseIfNotEmpty(string $variable) : Mailcode_Commands_Command_ElseIf_NotEmpty
    {
        return self::$commandSets->elseIf()->elseIfNotEmpty($variable);
    }
    
    public static function elseIfVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVar($variable, $operand, $value, $quoteValue);
    }

    public static function elseIfVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVarString($variable, $operand, $value);
    }
    
    public static function elseIfVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVarEquals($variable, $value, $quoteValue);
    }

    public static function elseIfVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVarEqualsString($variable, $value);
    }
    
    public static function elseIfVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVarNotEquals($variable, $value, $quoteValue);
    }

    public static function elseIfVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        return self::$commandSets->elseIf()->elseIfVarNotEqualsString($variable, $value);
    }

    public static function ifBeginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_BeginsWith
    {
        return self::$commandSets->if()->ifBeginsWith($variable, $search, $caseInsensitive);
    }

    public static function ifEndsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_EndsWith
    {
        return self::$commandSets->if()->ifEndsWith($variable, $search, $caseInsensitive);
    }

    public static function elseIfBeginsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_BeginsWith
    {
        return self::$commandSets->elseIf()->elseIfBeginsWith($variable, $search, $caseInsensitive);
    }
    
    public static function elseIfEndsWith(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_EndsWith
    {
        return self::$commandSets->elseIf()->elseIfEndsWith($variable, $search, $caseInsensitive);
    }
    
    public static function ifContains(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
    {
        return self::$commandSets->if()->ifContains($variable, $search, $caseInsensitive);
    }
    
    public static function elseIfContains(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_Contains
    {
        return self::$commandSets->elseIf()->elseIfContains($variable, $search, $caseInsensitive);
    }
    
    public static function ifEmpty(string $variable) : Mailcode_Commands_Command_If_Empty
    {
        return self::$commandSets->if()->ifEmpty($variable);
    }
    
    public static function ifNotEmpty(string $variable) : Mailcode_Commands_Command_If_NotEmpty
    {
        return self::$commandSets->if()->ifNotEmpty($variable);
    }
    
   /**
    * Creates a renderer instance, which can be used to easily
    * create and convert commands to strings.
    * 
    * @return Mailcode_Renderer
    */
    public static function createRenderer() : Mailcode_Renderer
    {
        return new Mailcode_Renderer();
    }
    
   /**
    * Creates a printer instance, which works like the renderer,
    * but outputs the generated strings to standard output.
    * 
    * @return Mailcode_Printer
    */
    public static function createPrinter() : Mailcode_Printer
    {
        return new Mailcode_Printer();
    }
    
   /**
    * Gets/creates the global instance of the date format info
    * class, used to handle date formatting aspects.
    * 
    * @return Mailcode_Date_FormatInfo
    */
    public static function createDateInfo() : Mailcode_Date_FormatInfo
    {
        return Mailcode_Date_FormatInfo::getInstance();
    }
    
    public static function init() : void
    {
        if(!isset(self::$commandSets))
        {
            self::$commandSets = new Mailcode_Factory_CommandSets();
        }
    }
}

Mailcode_Factory::init();
