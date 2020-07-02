<?php
/**
 * File containing the {@see Mailcode_Commands_LogicKeywords_Keyword} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_LogicKeywords_Keyword
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Container for individual sub-commands, along with the
 * exact logic keyword string used to append the command. 
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_LogicKeywords_Keyword extends OperationResult
{
    const ERROR_CANNOT_GET_INVALID_COMMAND = 60601;
    const ERROR_CANNOT_OVERWRITE_PARAMETERS = 60602;
    
   /**
    * @var Mailcode_Commands_LogicKeywords
    */
    private $keywords;
    
   /**
    * @var string
    */
    private $name;
    
   /**
    * @var string
    */
    private $type;
    
   /**
    * @var string
    */
    private $matchedString;
    
   /**
    * @var string
    */
    private $params = '';
    
   /**
    * @var boolean
    */
    private $paramsSet = false;
    
   /**
    * @var Mailcode_Collection
    */
    private $collection;
    
    public function __construct(Mailcode_Commands_LogicKeywords $keywords, string $name, string $matchedString, string $type)
    {
        $this->type = $type;
        $this->name = $name;
        $this->keywords = $keywords;
        $this->matchedString = $matchedString;
    }
    
   /**
    * The keyword name, e.g. "and". Always lowercase.
    * @return string
    */
    public function getName() : string
    {
        return $this->name;
    }
    
   /**
    * The full string that was matched in the command's parameters
    * string. Examples: "and:", "and variable:"...
    * 
    * @return string
    */
    public function getMatchedString() : string
    {
        return $this->matchedString;
    }
    
   /**
    * Sets the parameters string matching this logic keyword,
    * which is used to build the actual sub-command. Set by
    * the LogicKeywords class instance.
    * 
    * @param string $params
    */
    public function setParamsString(string $params) : void
    {
        if($this->paramsSet)
        {
            throw new Mailcode_Exception(
                'Cannot set parameters twice',
                'The setParamsString() method is only called once by the keywords class, and may not be called again.',
                self::ERROR_CANNOT_OVERWRITE_PARAMETERS
            );
        }
        
        $this->params = $params;
        $this->paramsSet = true;
        
        $this->createCommand();
    }
    
   /**
    * Retrieves the full command string used to create 
    * the actual command. For opening commands like IF,
    * this includes the closing {end} command.
    * 
    * @return string
    */
    public function getCommandString() : string
    {
        $command = $this->keywords->getCommand();
        
        $string = sprintf(
            '{%s %s: %s}',
            $this->keywords->getCommand()->getName(),
            $this->type,
            $this->params
        );
        
        // automatically close opening commands to be able to parse valid strings.
        if($command instanceof Mailcode_Commands_Command_Type_Opening)
        {
            $string .= '{end}';
        }
        
        return $string;
    }
    
    private function createCommand() : void
    {
        $this->collection = Mailcode::create()->parseString($this->getCommandString());
        
        if(!$this->collection->isValid())
        {
            $error = $this->collection->getFirstError();
            
            $this->makeError($error->getMessage(), $error->getCode());
        }
    }
    
   /**
    * Retrieves the command for the keyword.
    * 
    * @throws Mailcode_Exception
    * @return Mailcode_Commands_Command
    */
    public function getCommand() : Mailcode_Commands_Command
    {
        $command = $this->collection->getFirstCommand();
        
        if($this->collection->isValid() && $command !== null)
        {
            return $command;
        }
        
        throw new Mailcode_Exception(
            'Cannot get invalid command',
            'The collection has no commands, meaning the generated command was invalid.',
            self::ERROR_CANNOT_GET_INVALID_COMMAND
        );
    }
}
