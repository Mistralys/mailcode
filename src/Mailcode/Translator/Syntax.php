<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for a translator syntax, allowing the translation
 * of mailcode commands into this syntax.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Translator_Syntax
{
    const ERROR_UNKNOWN_COMMAND_TYPE = 50401;
    
   /**
    * Retrieves the syntax' type ID, e.g. "ApacheVelocity".
    * @return string
    */
    public function getTypeID() : string
    {
        $parts = explode('_', get_class($this));
        
        return array_pop($parts);
    }
    
   /**
    * Translates a single command to the target syntax.
    * 
    * @param Mailcode_Commands_Command $command
    * @throws Mailcode_Translator_Exception
    * @return string
    */
    public function translateCommand(Mailcode_Commands_Command $command) : string
    {
        $id = $command->getID();
        
        $method = '_translate'.$id;
        
        if(!method_exists($this, $method))
        {
            throw new Mailcode_Translator_Exception(
                'Unknown command type in translator',
                sprintf(
                    'The method [%s] does not exist in [%s].',
                    $method,
                    get_class($this)
                ),
                self::ERROR_UNKNOWN_COMMAND_TYPE
            );
        }
        
        return $this->$method($command);
    }
    
   /**
    * Translates all safeguarded commands in the subject string to the 
    * target syntax in one go.
    * 
    * @param Mailcode_Parser_Safeguard $safeguard
    * @return string
    */
    public function translateSafeguard(Mailcode_Parser_Safeguard $safeguard) : string
    {
        $subject = $safeguard->makeSafe();
        
        if(!$safeguard->hasPlaceholders())
        {
            return $subject;
        }
        
        $placeholders = $safeguard->getPlaceholders();
        
        $replaces = array();
        
        foreach($placeholders as $placeholder)
        {
            $replaces[$placeholder->getReplacementText()] = $this->translateCommand($placeholder->getCommand());
        }
            
        return str_replace(array_keys($replaces), array_values($replaces), $subject);
    }
    
    abstract protected function _translateShowVariable(Mailcode_Commands_Command_ShowVariable $command) : string;
        
    abstract protected function _translateSetVariable(Mailcode_Commands_Command_SetVariable $command) : string;
    
    abstract protected function _translateIf(Mailcode_Commands_Command_If $command) : string;
    
    abstract protected function _translateEnd(Mailcode_Commands_Command_End $command) : string;
        
    abstract protected function _translateElse(Mailcode_Commands_Command_Else $command) : string;
        
    abstract protected function _translateElseIf(Mailcode_Commands_Command_ElseIf $command) : string;
    
    abstract protected function _translateFor(Mailcode_Commands_Command_For $command) : string;
}
