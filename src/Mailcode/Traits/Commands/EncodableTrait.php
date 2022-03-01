<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\EncodableTrait}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Traits\Commands\EncodableTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Interfaces\Commands\Validation\IDNDecodeInterface;
use Mailcode\Interfaces\Commands\Validation\IDNEncodeInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Interfaces_Commands_Validation_URLDecode;
use Mailcode\Mailcode_Interfaces_Commands_Validation_URLEncode;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Trait used to implement the encodable interface.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see EncodableInterface
 */
trait EncodableTrait
{
    public function supportsEncoding(string $keyword) : bool
    {
        return in_array($keyword, $this->getSupportedEncodings(), true);
    }

    public function isEncodingEnabled(string $keyword) : bool
    {
        return $this->requireParams()
            ->getInfo()
            ->hasKeyword($keyword);
    }

    /**
     * @param string $keyword
     * @param bool $enabled
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setEncodingEnabled(string $keyword, bool $enabled) : self
    {
        if(!$this->supportsEncoding($keyword))
        {
            throw new Mailcode_Exception(
                'Cannot set encoding status, command does not support target encoding.',
                sprintf(
                    'Tried setting the encoding [%s], but the command only supports the following encodings: [%s].',
                    $keyword,
                    implode(', ', $this->getSupportedEncodings())
                ),
                EncodableInterface::ERROR_UNSUPPORTED_ENCODING
            );
        }

        $this->requireParams()
            ->getInfo()
            ->setKeywordEnabled($keyword, $enabled);

        return $this;
    }

    public function getActiveEncodings() : array
    {
        $keywords = $this->requireParams()
            ->getInfo()
            ->getKeywords();

        $result = array();

        // We are using the keywords list from the command's parameters
        // here, so they are in the exact order in which they were specified.
        foreach ($keywords as $keyword)
        {
            $name = $keyword->getKeyword();

            if($this->supportsEncoding($name) && $this->isEncodingEnabled($name))
            {
                $result[] = $name;
            }
        }

        return $result;
    }

    public function getSupportedEncodings() : array
    {
        $encodings = array();

        if($this instanceof IDNEncodeInterface)
        {
            $encodings[] = Mailcode_Commands_Keywords::TYPE_IDN_ENCODE;
        }

        if($this instanceof IDNDecodeInterface)
        {
            $encodings[] = Mailcode_Commands_Keywords::TYPE_IDN_DECODE;
        }

        if($this instanceof Mailcode_Interfaces_Commands_Validation_URLEncode)
        {
            $encodings[] = Mailcode_Commands_Keywords::TYPE_URLENCODE;
        }

        if($this instanceof Mailcode_Interfaces_Commands_Validation_URLDecode)
        {
            $encodings[] = Mailcode_Commands_Keywords::TYPE_URLDECODE;
        }

        return $encodings;
    }

    public function getEncodingToken(string $keyword) : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->requireParams()
            ->getInfo()
            ->getKeywordsCollection()
            ->getByName($keyword);
    }
}
