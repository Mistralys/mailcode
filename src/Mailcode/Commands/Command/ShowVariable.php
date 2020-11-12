<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowVariable
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
class Mailcode_Commands_Command_ShowVariable extends Mailcode_Commands_ShowBase
{
    const VALIDATION_TOO_MANY_PARAMETERS = 69701;

    public function getName() : string
    {
        return 'showvar';
    }
    
    public function getLabel() : string
    {
        return t('Show variable');
    }
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'urlencode',
            'no_other_tokens'
        );
    }
    
    protected function validateSyntax_no_other_tokens() : void
    {
        $tokens = $this->params->getInfo()->getTokens();

        $count = 1;

        if(isset($this->urlencodeToken))
        {
            $count = 2;
        }

        if(count($tokens) > $count)
        {
            $this->validationResult->makeError(
                t('Unknown parameters found:').' '.
                t('Only the variable name should be specified.'),
                self::VALIDATION_TOO_MANY_PARAMETERS
            );
        }
    }
}
