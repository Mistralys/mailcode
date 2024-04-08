<?php
/**
 * File containing the {@see \Mailcode\Translator\Syntax} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Translator\Syntax
 */

declare(strict_types=1);

namespace Mailcode\Translator;

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Safeguard;
use Mailcode\Mailcode_Translator_Exception;

/**
 * Abstract base class for a translator syntax, allowing the translation
 * of mailcode commands into this syntax.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Syntax
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
    * Retrieves the syntax's type ID, e.g. "ApacheVelocity".
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

    /**
     * @param Mailcode_Commands_Command $command
     * @return BaseCommandTranslation
     * @throws Mailcode_Translator_Exception
     */
    public function createTranslator(Mailcode_Commands_Command $command) : BaseCommandTranslation
    {
        $class = sprintf(
            __CLASS__ .'\%s\%sTranslation',
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

        if($translator instanceof BaseCommandTranslation)
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
