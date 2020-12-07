<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_DelimiterValidator} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_DelimiterValidator
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;
use stdClass;

/**
 * Utility class used to validate a safeguard placeholder delimiter
 * string, to ensure it conforms to the placeholder requirements.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_DelimiterValidator extends OperationResult
{
    const ERROR_EMPTY_DELIMITER = 73601;
    const ERROR_DELIMITER_TOO_SHORT = 73602;
    const ERROR_DELIMITER_URLENCODE_INCOMPATIBLE = 73603;

    /**
     * @var string
     */
    private $delimiter;

    public function __construct(string $delimiter)
    {
        parent::__construct(new stdClass());

        $this->delimiter = $delimiter;

        $this->validate();
    }

    private function validate() : OperationResult
    {
        if(empty($this->delimiter))
        {
            return $this->makeError(
                'Delimiters may not be empty.',
                self::ERROR_EMPTY_DELIMITER
            );
        }

        if(strlen($this->delimiter) < 2)
        {
            return $this->makeError(
                'The delimiter must have at least 2 characters in length.',
                self::ERROR_DELIMITER_TOO_SHORT
            );
        }

        $encoded = urlencode($this->delimiter);

        if($encoded !== $this->delimiter)
        {
            return $this->makeError(
                'The delimiter is not URL encoding neutral: it must not be modified by a urlencode() call.',
                self::ERROR_DELIMITER_URLENCODE_INCOMPATIBLE
            );
        }

        return $this;
    }

    public function throwExceptionIfInvalid() : void
    {
        if($this->isValid()) {
            return;
        }

        throw new Mailcode_Exception(
            $this->getErrorMessage(),
            'Delimiter: ['.$this->delimiter.']',
            $this->getCode()
        );
    }
}
