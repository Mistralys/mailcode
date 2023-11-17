<?php
/**
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Mailcode_Factory_CommandSets_Set_Variables
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command set used to create variable instances.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_CommandSets_Set_Variables extends Mailcode_Factory_CommandSets_Set
{
    private Mailcode_Variables $variables;

    protected function init(): void
    {
        $this->variables = Mailcode::create()->createVariables();
    }

    public function fullName(string $fullName) : Mailcode_Variables_Variable
    {
        return $this->variables->createVariableByName($fullName);
    }

    public function pathName(string $path, ?string $name=null) : Mailcode_Variables_Variable
    {
        return $this->variables->createVariable($path, $name);
    }
}
