<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory
{
    const ERROR_INVALID_COMMAND_CREATED = 50001;
    
    const ERROR_UNEXPECTED_COMMAND_TYPE = 50002;
    
   /**
    * Creates a ShowVariable command.
    * 
    * @param string $variableName A variable name, with or without the $ sign prepended.
    * @return Mailcode_Commands_Command_ShowVariable
    */
    public static function showVariable(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        $variableName = self::_filterVariableName($variableName);
        
        $cmd = new Mailcode_Commands_Command_ShowVariable(
            '',
            $variableName,
            '{showvar:'.$variableName.'}'
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
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
    public static function setVariable(string $variableName, string $value, bool $quoteValue=true) : Mailcode_Commands_Command_SetVariable
    {
        $variableName = self::_filterVariableName($variableName);
        
        if($quoteValue)
        {
            $value = self::_quoteString($value);
        }
        
        $params = $variableName.' = '.$value;
        
        $cmd = new Mailcode_Commands_Command_SetVariable(
            '', // type
            $params,
            '{setvar: '.$params.'}'
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
    }
    
    public static function comment(string $comments) : Mailcode_Commands_Command_Comment
    {
        $cmd = new Mailcode_Commands_Command_Comment(
            '', // type,
            $comments, // params,
            sprintf(
                '{comment: %s}',
                $comments
            )
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
    }
    
    public static function else() : Mailcode_Commands_Command_Else
    {
        $cmd = new Mailcode_Commands_Command_Else(
            '', // type,
            '', // params,
            '{else}'
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
    }
    
    public static function end() : Mailcode_Commands_Command_End
    {
        $cmd = new Mailcode_Commands_Command_End(
            '', // type,
            '', // params,
            '{end}'
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
    }
    
    protected static function _buildIf(string $cmd, string $condition, string $type='') : Mailcode_Commands_Command
    {
        $stringType = $type;
        
        if(!empty($type))
        {
            $stringType = ' '.$type;
        }
        
        $class = '\Mailcode\Mailcode_Commands_Command_'.$cmd;
        
        $cmd = new $class(
            $type, // type,
            $condition, // params,
            sprintf(
                '{%s%s: %s}',
                strtolower($cmd),
                $stringType,
                $condition
            )
        );
        
        self::_checkCommand($cmd);
        
        return $cmd;
    }
  
    protected static function _buildIfVar(string $cmd, string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command
    {
        if($quoteValue)
        {
            $value = self::_quoteString($value);
        }
        
        $condition = sprintf(
            "%s %s %s",
            self::_filterVariableName($variable),
            $operand,
            $value
        );
        
        return self::_buildIf($cmd, $condition, 'variable');
    }
    
    public static function if(string $condition, string $type='') : Mailcode_Commands_Command_If
    {
        $cmd = self::_buildIf('If', $condition, $type);
        
        if($cmd instanceof Mailcode_Commands_Command_If)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function ifVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If
    {
        $cmd = self::_buildIfVar('If', $variable, $operand, $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_If)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function ifVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If
    {
        $cmd = self::_buildIfVar('If', $variable, '==', $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_If)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function ifVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If
    {
        $cmd = self::_buildIfVar('If', $variable, '!=', $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_If)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function elseIf(string $condition, string $type='') : Mailcode_Commands_Command_ElseIf
    {
        $cmd = self::_buildIf('ElseIf', $condition, $type);
        
        if($cmd instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function elseIfVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf
    {
        $cmd = self::_buildIfVar('ElseIf', $variable, $operand, $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function elseIfVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf
    {
        $cmd = self::_buildIfVar('ElseIf', $variable, '==', $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    public static function elseIfVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf
    {
        $cmd = self::_buildIfVar('ElseIf', $variable, '!=', $value, $quoteValue);
        
        if($cmd instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType($cmd);
    }
    
    protected static function _filterVariableName(string $name) : string
    {
        return '$'.ltrim($name, '$');
    }
    
   /**
    * Quotes a string literal: adds the quotes, and escapes any quotes already present in it.
    * 
    * @param string $string
    * @return string
    */
    protected static function _quoteString(string $string) : string
    {
        return '"'.str_replace('"', '\"', $string).'"';
    }
    
    protected static function _checkCommand(Mailcode_Commands_Command $command) : void
    {
        if($command->isValid())
        {
            return;
        }
        
        throw new Mailcode_Factory_Exception(
            'Invalid command created.',
            'Validation message: '.$command->getValidationResult()->getErrorMessage(),
            self::ERROR_INVALID_COMMAND_CREATED,
            null,
            $command
        );
    }
    
    protected static function _exceptionUnexpectedType(Mailcode_Commands_Command $command) : Mailcode_Factory_Exception
    {
        return new Mailcode_Factory_Exception(
            'Invalid command class type created.',
            null,
            self::ERROR_UNEXPECTED_COMMAND_TYPE,
            null,
            $command
        );
    }
}
