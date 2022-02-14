<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_Code} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_Code
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening CODE statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_Code
    extends Mailcode_Commands_Command
    implements
        Mailcode_Commands_Command_Type_Opening,
        Mailcode_Interfaces_Commands_ProtectedContent
{
    use Mailcode_Traits_Commands_ProtectedContent;
    use Mailcode_Traits_Commands_Type_Opening;

    public const ERROR_LANG_TOKEN_MISSING = 73101;

    public const VALIDATION_LANGUAGE_NOT_SPECIFIED = 72901;
    public const VALIDATION_UNKNOWN_LANGUAGE = 72902;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
     */
    private $langToken = null;

    public function getName() : string
    {
        return 'code';
    }
    
    public function getLabel() : string
    {
        return t('Raw backend code');
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
        return true;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }
    
    protected function getValidations() : array
    {
        return array(
            'token',
            'language'
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }

    /**
     * Ensures that the language token is present.
     */
    protected function validateSyntax_token() : void
    {
        $lang = $this->params->getInfo()->getStringLiteralByIndex(0);

        if($lang)
        {
            $this->langToken = $lang;
            return;
        }

        $translator = new Mailcode_Translator();

        $this->validationResult->makeError(
            t('The target language has to be specified in the first parameter of the command.').' '.
            t('Possible values are:').' '.
            '<code>'.implode('</code>, <code>', $translator->getSyntaxNames()).'</code>',
            self::VALIDATION_LANGUAGE_NOT_SPECIFIED
        );
    }

    /**
     * Ensures that the syntax specified in the language token
     * is a valid translator syntax name.
     */
    protected function validateSyntax_language() : void
    {
        // To keep PHPStan happy. If no token has been found, this
        // method will not be called.
        if(!isset($this->langToken))
        {
            return;
        }

        $name = $this->langToken->getText();
        $translator = new Mailcode_Translator();

        if($translator->syntaxExists($name))
        {
            return;
        }

        $this->validationResult->makeError(
            t('The language %1$s does not exist.', '<code>'.$name.'</code>').' '.
            t('Possible values are:').' '.
            '<code>'.implode('</code>, <code>', $translator->getSyntaxNames()).'</code>',
            self::VALIDATION_UNKNOWN_LANGUAGE
        );
    }

    /**
     * Retrieves the name of the syntax that the code is written in.
     *
     * @return string
     * @throws Mailcode_Exception If the command has no language parameter.
     *
     * @see Mailcode_Commands_Command_Code::ERROR_LANG_TOKEN_MISSING
     */
    public function getSyntaxName() : string
    {
        if(isset($this->langToken))
        {
            return $this->langToken->getText();
        }

        throw new Mailcode_Exception(
            'No language name available',
            'The command has no lang token.',
            self::ERROR_LANG_TOKEN_MISSING
        );
    }

    /**
     * Retrieves an instance of the translation syntax used for
     * the code contained in the command.
     *
     * @return Mailcode_Translator_Syntax
     * @throws Mailcode_Exception
     */
    public function getSyntax() : Mailcode_Translator_Syntax
    {
        $translator = new Mailcode_Translator();
        return $translator->createSyntax($this->getSyntaxName());
    }
}
