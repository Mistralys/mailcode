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
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            Mailcode_Interfaces_Commands_Validation_URLEncode::VALIDATION_NAME_URLENCODE,
            Mailcode_Interfaces_Commands_Validation_URLDecode::VALIDATION_NAME_URLDECODE,
            'no_other_tokens'
        );
    }
    
    protected function validateSyntax_no_other_tokens() : void
    {
        $tokens = $this->params->getInfo()->getTokens();
        $allowed = $this->resolveActiveTokens();

        if(count($tokens) > count($allowed))
        {
            $this->validationResult->makeError(
                t('Unknown parameters found:').' '.
                t('Only the variable name and keywords should be specified.'),
                self::VALIDATION_TOO_MANY_PARAMETERS
            );
        }
    }

    /**
     * Gets all validated tokens that the command supports
     * (namely the variable, and keywords).
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token[]
     * @throws Mailcode_Exception
     */
    protected function resolveActiveTokens() : array
    {
        $allowed = array($this->getVariableToken());

        $token = $this->getURLEncodeToken();
        if($token)
        {
            $allowed[] = $token;
        }

        $token = $this->getURLDecodeToken();
        if($token)
        {
            $allowed[] = $token;
        }

        return $allowed;
    }
}
