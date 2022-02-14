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
    public const ERROR_INVALID_COMMAND_CREATED = 50001;
    public const ERROR_UNEXPECTED_COMMAND_TYPE = 50002;

    public const URL_ENCODING_NONE = 'none';
    public const URL_ENCODING_ENCODE = 'encode';
    public const URL_ENCODING_DECODE = 'decode';

    /**
    * @var Mailcode_Factory_CommandSets
    */
    private static $commandSets;

    /**
     * Returns the command set for `show` commands.
     *
     * @return Mailcode_Factory_CommandSets_Set_Show
     */
    public static function show() : Mailcode_Factory_CommandSets_Set_Show
    {
        return self::$commandSets->show();
    }

    /**
     * Return the command set for `set` commands.
     *
     * @return Mailcode_Factory_CommandSets_Set_Set
     */
    public static function set() : Mailcode_Factory_CommandSets_Set_Set
    {
        return self::$commandSets->set();
    }

    /**
     * Return the command set for `if` commands.
     *
     * @return Mailcode_Factory_CommandSets_Set_If
     */
    public static function if() : Mailcode_Factory_CommandSets_Set_If
    {
        return self::$commandSets->if();
    }

    /**
     * Return the command set for `else if` commands.
     *
     * @return Mailcode_Factory_CommandSets_Set_ElseIf
     */
    public static function elseIf() : Mailcode_Factory_CommandSets_Set_ElseIf
    {
        return self::$commandSets->elseIf();
    }

    /**
     * Return the command set for `miscellaneous` commands.
     *
     * @return Mailcode_Factory_CommandSets_Set_Misc
     */
    public static function misc() : Mailcode_Factory_CommandSets_Set_Misc
    {
        return self::$commandSets->misc();
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
