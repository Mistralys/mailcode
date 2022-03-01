<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_Mono} class.
 *
 * @see \Mailcode\Mailcode_Commands_Command_Mono
 *@subpackage Commands
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Mailcode command: opening `mono` statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_Mono
    extends Mailcode_Commands_Command
    implements
        Mailcode_Commands_Command_Type_Opening,
        Mailcode_Interfaces_Commands_Validation_Multiline,
        Mailcode_Interfaces_Commands_PreProcessing
{
    use Mailcode_Traits_Commands_Validation_Multiline;
    use Mailcode_Traits_Commands_Type_Opening;

    public const ERROR_INVALID_CSS_CLASS_NAME = 82201;

    /**
     * @var string[]
     */
    protected array $classes = array();

    public function getName() : string
    {
        return 'mono';
    }
    
    public function getLabel() : string
    {
        return t('Format text as monospaced');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding() : bool
    {
        return false;
    }

    public function getDefaultType() : string
    {
        return '';
    }
    
    public function requiresParameters(): bool
    {
        return false;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }
    
    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Multiline::VALIDATION_NAME_MULTILINE,
            'class'
        );
    }

    protected function validateSyntax_class(): void
    {
        $literals = $this->requireParams()->getInfo()->getStringLiterals();

        if(empty($literals)) {
            return;
        }

        foreach($literals as $literal)
        {
            $parts = ConvertHelper::explodeTrim(' ', $literal->getText());

            foreach($parts as $part) {
                $this->addClass($part);
            }
        }
    }

    /**
     * @param string $className
     * @return bool
     */
    public function addClass(string $className) : bool
    {
        $className = trim($className);

        if($this->isClassNameValid($className))
        {
            if(!in_array($className, $this->classes, true)) {
                $this->classes[] = $className;
            }

            return true;
        }

        $this->validationResult->makeError(
            t('%1$s is not a valid CSS class name.', $className).' '.
            t('Allowed characters:').' '.
            t('Upper and lowercase letters, digits, underscores (_) and hyphens (-).').' '.
            t('May not start with a digit, two hyphens, or a hyphen followed by a digit.'),
            self::ERROR_INVALID_CSS_CLASS_NAME
        );

        return false;
    }

    public function isClassNameValid(string $className) : bool
    {
        $result = preg_match('/\A[a-z_][a-z0-9_][-_a-z0-9]+\Z/i', $className);

        return $result !== false && $result > 0;
    }

    public function generatesContent() : bool
    {
        return true;
    }

    public function preProcessOpening(): string
    {
        if($this->isMultiline()) {
            return sprintf('<pre%s>', $this->renderAttributes());
        }

        return sprintf('<code%s>', $this->renderAttributes());
    }

    public function preProcessClosing(): string
    {
        if($this->isMultiline()) {
            return '</pre>';
        }

        return '</code>';
    }

    private function renderAttributes() : string
    {
        if(empty($this->classes)) {
            return '';
        }

        return sprintf(' class="%s"', implode(' ', $this->classes));
    }
}
