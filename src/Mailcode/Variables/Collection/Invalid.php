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
    public const ERROR_NO_FIRST_ERROR_AVAILABLE = 101701;

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
        $first = $this->getFirst();

        if($first)
        {
            return $first->getValidationResult();
        }

        throw new Mailcode_Exception(
            'Cannot get first error, no errors present.',
            '',
            self::ERROR_NO_FIRST_ERROR_AVAILABLE
        );
    }
}
