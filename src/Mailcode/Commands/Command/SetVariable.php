<?php
/**
 * File containing the {@see Mailcode_Commands_Command_SetVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_SetVariable
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\Validation\CountInterface;
use Mailcode\Traits\Commands\Validation\CountTrait;

/**
 * Mailcode command: set a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_SetVariable
    extends Mailcode_Commands_Command
    implements
    Mailcode_Commands_Command_Type_Standalone,
    CountInterface
{
    use CountTrait;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
     */
    private $variableToken;

    public function getName(): string
    {
        return 'setvar';
    }

    public function getLabel(): string
    {
        return t('Set variable value');
    }

    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding(): bool
    {
        return false;
    }

    public function getDefaultType(): string
    {
        return '';
    }

    public function requiresParameters(): bool
    {
        return true;
    }

    public function supportsLogicKeywords(): bool
    {
        return false;
    }

    protected function getValidations(): array
    {
        return array(
            'variable',
            'operand',
            'assignment',
            CountInterface::VALIDATION_COUNT_NAME
        );
    }

    public function generatesContent(): bool
    {
        return false;
    }

    protected function validateSyntax_variable(): void
    {
        $val = $this->getValidator()->createVariable()->setIndex(0);

        if ($val->isValid()) {
            $this->variableToken = $val->getToken();
        } else {
            $this->validationResult->makeError(
                'The first parameter must be a variable name.',
                Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            );
        }
    }

    protected function validateSyntax_operand(): void
    {
        $tokens = $this->requireParams()
            ->getInfo()
            ->createPruner()
            ->limitToOperands()
            ->getTokens();

        foreach ($tokens as $token) {
            if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand) {
                $this->validateOperand($token);
            }
        }
    }

    protected function validateOperand(Mailcode_Parser_Statement_Tokenizer_Token_Operand $token): void
    {
        $allowed = Mailcode_Parser_Statement_Tokenizer_Token_Operand::getArithmeticSigns();
        $allowed[] = '=';

        $sign = $token->getSign();

        // ensure that the operand we have in the command is one of the
        // allowed ones.
        if (!in_array($sign, $allowed)) {
            $this->validationResult->makeError(
                t('The %1$s sign is not allowed in this command.', '<code>' . $sign . '</code>'),
                Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
            );
        }
    }

    protected function validateSyntax_assignment(): void
    {
        $tokens = $this->getAssignmentTokens();

        if (empty($tokens)) {
            $this->validationResult->makeError(
                t('No value assigned to the variable.'),
                Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            );
        }
    }

    public function getVariable(): Mailcode_Variables_Variable
    {
        if ($this->variableToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            return $this->variableToken->getVariable();
        }

        throw new Mailcode_Exception(
            'No variable found.',
            'Statement does not start with a variable: [' . $this->paramsString . ']',
            Mailcode_Commands_CommonConstants::ERROR_NO_VARIABLE_AVAILABLE
        );
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    public function getAssignmentTokens(): array
    {
        $params = $this->requireParams()->getInfo()->getTokens();

        array_shift($params); // variable

        $eq = array_shift($params); // equals sign

        // in case the equals sign was omitted.
        if ($eq !== null && !$eq instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand) {
            array_unshift($params, $eq);
        }

        return $params;
    }

    public function getAssignmentString(): string
    {
        $tokens = $this->getAssignmentTokens();

        $items = array();

        foreach ($tokens as $token) {
            $items[] = $token->getNormalized();
        }

        return implode(' ', $items);
    }
}
