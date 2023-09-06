<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\CountTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\CountTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\CountInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
use function Mailcode\t;

/**
 * Command validation drop-in: checks for the presence
 * of the `count` parameter in the command statement.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CountInterface
 */
trait CountTrait
{
    private bool $countEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token_Variable $countToken = null;

    protected function validateSyntax_check_count(): void
    {
        $this->countToken = null;

        $token = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(CountInterface::PARAMETER_NAME);

        if($token === null)
        {
            $this->countEnabled = false;
            return;
        }

        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $this->countToken = $token;
            $this->countEnabled = true;
            return;
        }

        $this->validationResult->makeError(
            t('Invalid count subject:') . ' ' . t('Expected a variable.'),
            CountInterface::VALIDATION_COUNT_CODE_WRONG_TYPE
        );
    }

    public function isCountEnabled(): bool
    {
        return $this->countEnabled;
    }

    public function getCountVariable(): ?Mailcode_Variables_Variable
    {
        if($this->countToken !== null)
        {
            return $this->countToken->getVariable();
        }

        return null;
    }

    /**
     * @param Mailcode_Variables_Variable|string|null $variable Set to null to remove the count.
     * @return $this
     */
    public function setCount($variable) : self
    {
        $this->countEnabled = false;
        $this->countToken = null;

        $info = $this->requireParams()->getInfo();

        if(isset($this->countToken)) {
            $info->removeToken($this->countToken);
        }

        if(is_string($variable)) {
            $variable = Mailcode_Factory::var()->fullName($variable);
        }

        if($variable !== null) {
            $this->countEnabled = true;
            $this->countToken = $info->addVariable($variable);
            $info->setParamName($this->countToken, CountInterface::PARAMETER_NAME);
        }

        return $this;
    }
}
