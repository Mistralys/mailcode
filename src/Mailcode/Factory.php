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
    * @var Mailcode_Renderer
    */
    protected static $renderer;
    
   /**
    * Creates a ShowVariable command.
    * 
    * @param string $variableName A variable name, with or without the $ sign prepended.
    * @return Mailcode_Commands_Command_ShowVariable
    */
    public static function showVar(string $variableName) : Mailcode_Commands_Command_ShowVariable
    {
        $variableName = self::_filterVariableName($variableName);
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowVariable',
            '',
            $variableName,
            '{showvar:'.$variableName.'}'
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowVariable)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('ShowVariable', $cmd);
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
        $variableName = self::_filterVariableName($variableName);
        
        $format = '';
        if(!empty($formatString))
        {
            $format = sprintf(
                ' "%s"',
                $formatString
            );
        }
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowDate',
            '',
            $variableName.$format,
            sprintf(
                '{showdate: %s%s}',
                $variableName,
                $format
            )
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowDate)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('ShowDate', $cmd);
    }
    

   /**
    * Creates a ShowSnippet command.
    *
    * @param string $snippetName A snippet name, with or without the $ sign prepended.
    * @return Mailcode_Commands_Command_ShowSnippet
    */
    public static function showSnippet(string $snippetName) : Mailcode_Commands_Command_ShowSnippet
    {
        $snippetName = self::_filterVariableName($snippetName);
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'ShowSnippet',
            '',
            $snippetName,
            '{showsnippet:'.$snippetName.'}'
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_ShowSnippet)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('ShowSnippet', $cmd);
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
        $variableName = self::_filterVariableName($variableName);
        
        if($quoteValue)
        {
            $value = self::_quoteString($value);
        }
        
        $params = $variableName.' = '.$value;
        
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'SetVariable',
            '', // type
            $params,
            '{setvar: '.$params.'}'
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_SetVariable)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('SetVariable', $cmd);
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
        return self::setVar($variableName, $value, true);
    }
    
    public static function comment(string $comments) : Mailcode_Commands_Command_Comment
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'Comment',
            '', // type
            $comments, // params
            sprintf(
                '{comment: %s}',
                $comments
            )
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_Comment)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('Comment', $cmd);
    }
    
    public static function else() : Mailcode_Commands_Command_Else
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'Else', 
            '', 
            '', 
            '{else}'
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_Else)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('Else', $cmd);
    }
    
    public static function end() : Mailcode_Commands_Command_End
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'End',
            '',
            '',
            '{end}'
        );
        
        self::_checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_End)
        {
            return $cmd;
        }
        
        throw self::_exceptionUnexpectedType('End', $cmd);
    }
    
    protected static function _buildIf(string $ifType, string $params, string $type='') : Mailcode_Commands_IfBase
    {
        $stringType = $type;
        
        if(!empty($type))
        {
            $stringType = ' '.$type;
        }
        
        $command = Mailcode::create()->getCommands()->createCommand(
            $ifType, 
            $type, 
            $params, 
            sprintf(
                '{%s%s: %s}',
                strtolower($ifType),
                $stringType,
                $params
            )
        );
        
        self::_checkCommand($command);
        
        if($command instanceof Mailcode_Commands_IfBase)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfBase', $command);
    }
  
    protected static function _buildIfVar(string $ifType, string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_IfBase
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
        
        return self::_buildIf($ifType, $condition, 'variable');
    }
    
    public static function if(string $condition, string $type='') : Mailcode_Commands_Command_If
    {
        $command = self::_buildIf('If', $condition, $type);
        
        if($command instanceof Mailcode_Commands_Command_If)
        {
            return $command;
        }
       
        throw self::_exceptionUnexpectedType('If', $command);
    }
    
    public static function ifVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = self::_buildIfVar('If', $variable, $operand, $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfVar', $command);
    }

    public static function ifVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_If_Variable
    {
        $command = self::_buildIfVar('If', $variable, $operand, $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfVarString', $command);
    }
    
    public static function ifVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = self::_buildIfVar('If', $variable, '==', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfVarEquals', $command);
    }

    public static function ifVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If
    {
        $command = self::_buildIfVar('If', $variable, '==', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfarEqualsString', $command);
    }
    
    public static function ifVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_If_Variable
    {
        $command = self::_buildIfVar('If', $variable, '!=', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfVarNotEquals', $command);
    }

    public static function ifVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_If_Variable
    {
        $command = self::_buildIfVar('If', $variable, '!=', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_If_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('IfVarNotEqualsString', $command);
    }
    
    public static function elseIf(string $condition, string $type='') : Mailcode_Commands_Command_ElseIf
    {
        $command = self::_buildIf('ElseIf', $condition, $type);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIf', $command);
    }
    
    public static function elseIfVar(string $variable, string $operand, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, $operand, $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVariable', $command);
    }

    public static function elseIfVarString(string $variable, string $operand, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, $operand, $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVarString', $command);
    }
    
    public static function elseIfVarEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, '==', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVarEquals', $command);
    }

    public static function elseIfVarEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, '==', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVarEqualsString', $command);
    }
    
    public static function elseIfVarNotEquals(string $variable, string $value, bool $quoteValue=false) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, '!=', $value, $quoteValue);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVarNotEquals', $command);
    }

    public static function elseIfVarNotEqualsString(string $variable, string $value) : Mailcode_Commands_Command_ElseIf_Variable
    {
        $command = self::_buildIfVar('ElseIf', $variable, '!=', $value, true);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Variable)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfVarNotEqualsString', $command);
    }
    
    public static function ifContains(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_If_Contains
    {
        $command = self::_buildIfContains('If', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_If_Contains)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfContains', $command);
    }
    
    public static function elseIfContains(string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_Command_ElseIf_Contains
    {
        $command = self::_buildIfContains('ElseIf', $variable, $search, $caseInsensitive);
        
        if($command instanceof Mailcode_Commands_Command_ElseIf_Contains)
        {
            return $command;
        }
        
        throw self::_exceptionUnexpectedType('ElseIfContains', $command);
    }
    
    protected static function _buildIfContains(string $ifType, string $variable, string $search, bool $caseInsensitive=false) : Mailcode_Commands_IfBase
    {
        $keyword = ' ';
        
        if($caseInsensitive)
        {
            $keyword = ' insensitive: ';
        }
        
        $condition = sprintf(
            '%s%s"%s"',
            self::_filterVariableName($variable),
            $keyword,
            $search
        );
        
        return self::_buildIf($ifType, $condition, 'contains');
    }
    
    protected static function _filterVariableName(string $name) : string
    {
        $name = preg_replace('/\s/', '', $name);
        
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
    
    protected static function _exceptionUnexpectedType(string $type, Mailcode_Commands_Command $command) : Mailcode_Factory_Exception
    {
        return new Mailcode_Factory_Exception(
            'Invalid command class type created.',
            sprintf('Excepted type [%s], but created class [%s].', $type, get_class($command)),
            self::ERROR_UNEXPECTED_COMMAND_TYPE,
            null,
            $command
        );
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
}
