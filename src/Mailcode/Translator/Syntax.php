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
class Mailcode_Translator_Syntax
{
    const ERROR_UNKNOWN_COMMAND_TYPE = 50401;
    
   /**
    * @var string
    */
    protected $typeID;
    
    public function __construct(string $typeID)
    {
        $this->typeID = $typeID;
    }
    
   /**
    * Retrieves the syntax' type ID, e.g. "ApacheVelocity".
    * @return string
    */
    public function getTypeID() : string
    {
        return $this->typeID;
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
        $translator = $this->createTranslator($command);
        
        return $translator->translate($command);
    }
    
    protected function createTranslator(Mailcode_Commands_Command $command) : Mailcode_Translator_Command
    {
        $class = sprintf(
            'Mailcode\Mailcode_Translator_Syntax_%s_%s',
            $this->getTypeID(),
            $command->getID()
        );
        
        if(!class_exists($class))
        {
            throw new Mailcode_Translator_Exception(
                sprintf('Unknown command %s in translator', $command->getID()),
                sprintf(
                    'The class [%s] does not exist.',
                    $class
                ),
                self::ERROR_UNKNOWN_COMMAND_TYPE
            );
        }
        
        $translator = new $class($command);
        
        return $translator;
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
}
