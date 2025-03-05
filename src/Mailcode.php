<?php
/**
 * File containing the {@see Mailcode} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper\FolderInfo;
use Psr\Log\LoggerInterface;

/**
 * Main hub for the "Mailcode" syntax handling, which is used
 * to abstract the actual command syntax used by the selected
 * mailing format.
 * 
 * Users only work with the mailcode commands to ensure that
 * the mail editor interface stays independent of the actual
 * format implementation used by the backend systems.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode
{
    public const PACKAGE_NAME = 'Mailcode';

    protected ?Mailcode_Parser $parser = null;
    protected ?Mailcode_Commands $commands = null;
    protected ?Mailcode_Variables $variables = null;

    private static ?FolderInfo $cacheFolder = null;

    public static function setCacheFolder(FolderInfo $cacheFolder) : void
    {
        self::$cacheFolder = $cacheFolder;
    }

    public static function getCacheFolder() : FolderInfo
    {
        if(isset(self::$cacheFolder)) {
            return self::$cacheFolder;
        }

        throw new Mailcode_Exception(
            'The cache folder has not been set.',
            sprintf(
                'The cache folder must be set with [%s] before using the library.',
                ConvertHelper::callback2string(array(__CLASS__, 'setCacheFolder'))
            ),
            Mailcode_Exception::ERROR_CACHE_FOLDER_NOT_SET
        );
    }

   /**
    * Creates a new mailcode instance.
    * @return Mailcode
    */
    public static function create() : Mailcode
    {
        return new Mailcode();
    }

    public static function getName() : string
    {
        return self::PACKAGE_NAME;
    }

    /**
    * Parses the string to detect all commands contained within.
    * 
    * @param string $string
    * @return Mailcode_Collection
    */
    public function parseString(string $string) : Mailcode_Collection
    {
        return $this->getParser()
            ->parseString($string)
            ->getCollection();
    }
    
   /**
    * Retrieves the string parser instance used to detect commands.
    * 
    * @return Mailcode_Parser
    */
    public function getParser() : Mailcode_Parser
    {
        if(!isset($this->parser)) 
        {
            $this->parser = new Mailcode_Parser($this);
        }
        
        return $this->parser;
    }
    
   /**
    * Retrieves the commands collection, which is used to
    * access information on the available commands.
    * 
    * @return Mailcode_Commands
    */
    public function getCommands() : Mailcode_Commands
    {
        if(!isset($this->commands)) 
        {
            $this->commands = new Mailcode_Commands();
        }
        
        return $this->commands;
    }
    
    public function createSafeguard(string $subject) : Mailcode_Parser_Safeguard
    {
        return $this->getParser()->createSafeguard($subject);
    }
    
    public function createString(string $subject) : Mailcode_StringContainer
    {
        return new Mailcode_StringContainer($subject);
    }

    /**
     * Attempts to find all variables in the target string.
     *
     * @param string $subject
     * @param Mailcode_Commands_Command|null $sourceCommand
     * @return Mailcode_Variables_Collection_Regular
     */
    public function findVariables(string $subject, ?Mailcode_Commands_Command $sourceCommand=null) : Mailcode_Variables_Collection_Regular
    {
        return $this->createVariables()->parseString($subject, $sourceCommand);
    }
    
    public function createVariables() : Mailcode_Variables
    {
        if(!isset($this->variables))
        {
            $this->variables = new Mailcode_Variables();
        }
        
        return $this->variables;
    }
    
   /**
    * Creates the translator, which can be used to convert commands
    * to another supported syntax.
    * 
    * @return Mailcode_Translator
    */
    public function createTranslator() : Mailcode_Translator
    {
        return Mailcode_Translator::create();
    }
    
   /**
    * Creates the styler, which can be used to retrieve the 
    * CSS required to style the highlighted commands in HTML.
    * 
    * @return Mailcode_Styler
    */
    public function createStyler() : Mailcode_Styler
    {
        return new Mailcode_Styler();
    }

    /**
     * Creates a new pre-processor instance for the specified content
     * string, to replace all pre-process enabled commands with their
     * corresponding contents.
     *
     * @param string $subject
     * @return Mailcode_PreProcessor
     */
    public function createPreProcessor(string $subject) : Mailcode_PreProcessor
    {
        return new Mailcode_PreProcessor($subject);
    }

    /**
     * @var LoggerInterface|NULL
     */
    private static ?LoggerInterface $logger = null;
    private static bool $debug = false;

    public static function setLogger(LoggerInterface $logger) : void
    {
        self::$logger = $logger;
    }

    public static function getLogger() : ?LoggerInterface
    {
        return self::$logger;
    }

    public static function setDebugging(bool $enabled=true) : void
    {
        self::$debug = $enabled;
    }

    public static function isDebugEnabled() : bool
    {
        return self::$debug;
    }

    /**
     * @param string $message
     * @param array<string|int,mixed> $context
     * @return void
     */
    public static function debug(string $message, array $context=array()) : void
    {
        if(self::$debug && isset(self::$logger))
        {
            self::$logger->debug($message, $context);
        }
    }
}
