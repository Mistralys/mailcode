<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowSnippet} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowSnippet
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowSnippet
    extends Mailcode_Commands_ShowBase
    implements
    Mailcode_Interfaces_Commands_Validation_NoHTML,
    NamespaceInterface
{
    use Mailcode_Traits_Commands_Validation_NoHTML;
    use NamespaceTrait;

    public function getName(): string
    {
        return 'showsnippet';
    }

    public function getLabel(): string
    {
        return t('Show text snippet');
    }

    protected function getValidations(): array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            Mailcode_Interfaces_Commands_Validation_NoHTML::VALIDATION_NAME_NOHTML,
            NamespaceInterface::VALIDATION_NAMESPACE_NAME
        );
    }

    public function generatesContent(): bool
    {
        return true;
    }
}
