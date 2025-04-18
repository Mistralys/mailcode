<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator;

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Safeguard;
use Mailcode\Mailcode_Translator_Exception;
use Mailcode\Translator\Syntax\ApacheVelocitySyntax;

/**
 * Abstract base class for a translator syntax, allowing the translation
 * of mailcode commands into this syntax.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class BaseSyntax implements SyntaxInterface
{
    public const ERROR_UNKNOWN_COMMAND_TYPE = 50401;
    public const ERROR_INVALID_COMMAND_INSTANCE = 50402;

    public function translateCommand(Mailcode_Commands_Command $command) : string
    {
        return $this->createTranslator($command)->translate($command);
    }

    /**
     * @inheritDoc
     * @throws Mailcode_Translator_Exception
     */
    public function createTranslator(Mailcode_Commands_Command $command) : BaseCommandTranslation
    {
        $class = sprintf(
            'Mailcode\Translator\Syntax\%s\%sTranslation',
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
        
        $translator = new $class($this);

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
     * @inheritDoc
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
