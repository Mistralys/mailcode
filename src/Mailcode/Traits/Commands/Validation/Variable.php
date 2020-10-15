<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Variable} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a variable name. Will accept the first variable 
 * it finds.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_Variable
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    protected $variableToken;
    
    protected function validateSyntax_variable() : void
    {
        $var = $this->validator->createVariable();
        
        if($var->isValid())
        {
            $this->variableToken = $var->getToken();
        }
        else
        {
            $this->validationResult->makeError(
                t('No variable has been specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            );
        }
    }
    
   /**
    * Retrieves the variable being compared.
    *
    * @return Mailcode_Variables_Variable
    */
    public function getVariable() : Mailcode_Variables_Variable
    {
        if($this->variableToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $this->variableToken->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No variable available',
            null,
            Mailcode_Commands_CommonConstants::ERROR_NO_VARIABLE_AVAILABLE
        );
    }
    
    public function getVariableName() : string
    {
        return $this->getVariable()->getFullName();
    }

    /**
     * Checks whether the command is nested in a loop (FOR) command.
     *
     * @return bool
     */
    public function isInLoop() : bool
    {
        return $this->getLoopCommand() !== null;
    }

    /**
     * Retrieves the command's parent loop command, if any.
     *
     * @return Mailcode_Commands_Command_For|NULL
     */
    public function getLoopCommand() : ?Mailcode_Commands_Command_For
    {
        return $this->findLoopRecursive($this);
    }

    /**
     * Recursively tries to find a loop command in the command's
     * parent commands. Goes up the whole ancestry if need be.
     *
     * @param Mailcode_Commands_Command $subject
     * @return Mailcode_Commands_Command_For|null
     */
    protected function findLoopRecursive(Mailcode_Commands_Command $subject) : ?Mailcode_Commands_Command_For
    {
        $parent = $subject->getParent();

        if($parent === null)
        {
            return null;
        }

        if($parent instanceof Mailcode_Commands_Command_For)
        {
            return $parent;
        }

        return $this->findLoopRecursive($parent);
    }
}
