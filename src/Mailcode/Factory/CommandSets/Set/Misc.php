<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_Misc} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory_CommandSets_Set_Misc
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
class Mailcode_Factory_CommandSets_Set_Misc extends Mailcode_Factory_CommandSets_Set
{
    /**
     * @param string $comments Quoted or unquoted string.
     * @return Mailcode_Commands_Command_Comment
     * @throws Mailcode_Exception
     * @throws Mailcode_Factory_Exception
     */
    public function comment(string $comments) : Mailcode_Commands_Command_Comment
    {
        $cmd = $this->commands->createCommand(
            'Comment',
            '', // type
            $this->instantiator->quoteString($comments),
            sprintf(
                '{comment: "%s"}',
                $this->instantiator->quoteString($comments)
            )
        );
    
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_Comment)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('Comment', $cmd);
    }

    /**
     * Creates a for loop command.
     *
     * @param string $sourceVariable
     * @param string $loopVariable
     * @return Mailcode_Commands_Command_For
     * @throws Mailcode_Exception
     * @throws Mailcode_Factory_Exception
     */
    public function for(string $sourceVariable, string $loopVariable) : Mailcode_Commands_Command_For
    {
        $sourceVariable = '$'.ltrim($sourceVariable, '$');
        $loopVariable = '$'.ltrim($loopVariable, '$');
        
        $cmd = $this->commands->createCommand(
            'For', 
            '', 
            sprintf(
                '%s in: %s',
                $loopVariable,
                $sourceVariable
            ), 
            sprintf(
                '{for: %s in: %s}',
                $loopVariable,
                $sourceVariable
            )
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_For)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('For', $cmd);
    }

    /**
     * Creates a break command, which can be used to break out of
     * a loop command. Using this outside of a loop will trigger
     * a validation error.
     *
     * @return Mailcode_Commands_Command_Break
     * @throws Mailcode_Exception
     * @throws Mailcode_Factory_Exception
     */
    public function break() : Mailcode_Commands_Command_Break
    {
        $cmd = $this->commands->createCommand(
            'Break',
            '',
            '',
            '{break}'
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_Break)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('Break', $cmd);
    }

    /**
     * Creates a preprocessor command to format text as preformatted.
     *
     * NOTE: Requires the Mailcode text to be preprocessed using the
     * preprocessor. See the documentation on how to use it.
     *
     * @param bool $multiline
     * @param string[] $classes
     * @return Mailcode_Commands_Command_Mono
     * @throws Mailcode_Exception
     * @throws Mailcode_Factory_Exception
     */
    public function mono(bool $multiline=false, array $classes=array()) : Mailcode_Commands_Command_Mono
    {
        $params = '';
        $source = '{code';

        if($multiline) {
            $params = 'multiline:';
            $source = '{code: multiline:';
        }

        if(!empty($classes)) {
            $classString = sprintf('"%s"', implode(' ', $classes));
            $params .= $classString;
            $source .= $classString;
        }

        $source .= '}';

        $cmd = $this->commands->createCommand(
            'Mono',
            '',
            $params,
            $source
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_Mono)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('Mono', $cmd);
    }

    public function code(string $language, string $content) : Mailcode_Commands_Command_Code
    {
        $contentID = Mailcode_Parser_PreParser::storeContent($content);

        $cmd = $this->commands->createCommand(
            'Code',
            '',
            sprintf(
                '%s "%s"',
                $contentID,
                $language
            ),
            sprintf(
                '{code: %s "%s"}',
                $contentID,
                $language
            )
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_Code)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('Code', $cmd);
    }
}
