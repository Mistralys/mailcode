<?php
/**
 * File containing the {@see Mailcode_Variables_Collection} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see Mailcode_Variables_Collection
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Handler for all variable-related tasks.
 *
 * @package Mailcode
 * @subpackage Variables
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Variables_Collection_Invalid extends Mailcode_Variables_Collection
{
    public function add(Mailcode_Variables_Variable $variable) : Mailcode_Variables_Collection
    {
        if($variable->isValid())
        {
            return $this;
        }
        
        return parent::add($variable);
    }
    
    public function getFirstError() : OperationResult
    {
        return $this->getFirst()->getValidationResult();
    }
        
    public function getFirst() : Mailcode_Variables_Variable
    {
        reset($this->variables);
        
        return $this->variables[key($this->variables)];
    }
}
