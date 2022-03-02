<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_Commands_Command_ShowEncoded}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_ShowEncoded
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Commands\ParamsException;
use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Interfaces\Commands\Validation\IDNEncodeInterface;
use Mailcode\Interfaces\Commands\Validation\IDNEncodingInterface;
use Mailcode\Traits\Commands\EncodableTrait;
use Mailcode\Traits\Commands\Validation\IDNDecodeTrait;
use Mailcode\Traits\Commands\Validation\IDNEncodeTrait;

/**
 * Command used to encode bits of text to any number
 * of supported encodings, in the order that the encoding
 * keywords are added to the command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowEncoded
    extends Mailcode_Commands_ShowBase
    implements
    IDNEncodingInterface
{
    use IDNEncodeTrait;
    use IDNDecodeTrait;

    public const VALIDATION_MISSING_SUBJECT_STRING = 102201;

    public const ERROR_NO_TEXT_TOKEN_AVAILABLE = 102301;

    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $textToken = null;

    public function getName() : string
    {
        return 'showencoded';
    }

    public function getLabel() : string
    {
        return t('Encode text using a variety of formats.');
    }

    protected function getValidations() : array
    {
        return array(
            'subject_text',
            'require_encoding'
        );
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
     * @throws ParamsException
     */
    public function getTextToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        if(isset($this->textToken))
        {
            return $this->textToken;
        }

        throw new ParamsException(
            'No text token available in the command.',
            '',
            self::ERROR_NO_TEXT_TOKEN_AVAILABLE
        );
    }

    public function getText() : string
    {
        return $this->getTextToken()->getText();
    }

    public function setText(string $text) : self
    {
        if(isset($this->textToken))
        {
            $this->textToken->setText($text);
            return $this;
        }

        $this->textToken = $this->requireParams()
            ->getInfo()
            ->addStringLiteral($text);

        return $this;
    }

    protected function validateSyntax_subject_text() : void
    {
        $strings = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        if(empty($strings))
        {
            $this->getValidationResult()->makeError(
                t('No text to encode has been specified.'),
                self::VALIDATION_MISSING_SUBJECT_STRING
            );
            return;
        }

        $this->textToken = $strings[0];
    }

    protected function validateSyntax_require_encoding() : void
    {
        $keywords = $this->requireParams()
            ->getInfo()
            ->getKeywords();

        if(!empty($keywords))
        {
            return;
        }

        $this->getValidationResult()->makeError(
            t('No encodings have been specified.'),
            Mailcode_Commands_CommonConstants::VALIDATION_NO_ENCODINGS_SPECIFIED
        );
    }
}
