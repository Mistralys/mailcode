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
    public const ERROR_UNKNOWN_COMMAND_TYPE = 50401;
    public const ERROR_INVALID_COMMAND_INSTANCE = 50402;
    
   /**
    * @var string
    */
    protected string $typeID;
    
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
        return $this->createTranslator($command)->translate($command);
    }
    
    public function createTranslator(Mailcode_Commands_Command $command) : Mailcode_Translator_Command
    {
        $class = sprintf(
            Mailcode_Translator_Syntax::class.'_%s_%s',
            $this->getTypeID(),
            $command->getID()
        );

        if(!class_exists($class))
        {
            $class = sprintf(
                'Mailcode\Translator\Syntax\%s\%s',
                $this->getTypeID(),
                $command->getID()
            );
        }
        
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

        if($translator instanceof Mailcode_Translator_Command)
        {
            return $translator;
        }

        throw new Mailcode_Translator_Exception(
            'Invalid translator command instance.',
            sprintf(
                'The class [%s] does not extend the base translator command class.',
                get_class($translator)
            ),
            self::ERROR_INVALID_COMMAND_INSTANCE
        );
    }

    /**
     * Translates all safeguarded commands in the subject string to the
     * target syntax in one go.
     *
     * @param Mailcode_Parser_Safeguard $safeguard
     * @return string
     * @throws Mailcode_Exception
     * @throws Mailcode_Translator_Exception
     */
    public function translateSafeguard(Mailcode_Parser_Safeguard $safeguard) : string
    {
        $subject = $safeguard->makeSafe();
        
        if(!$safeguard->hasPlaceholders())
        {
            return $subject;
        }
        
        $placeholders = $safeguard->getPlaceholdersCollection()->getAll();
        
        $replaces = array();
        
        foreach($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();

            $replaces[$placeholder->getReplacementText()] = $this->translateCommand($command);
        }
            
        return str_replace(array_keys($replaces), array_values($replaces), $subject);
    }
}
