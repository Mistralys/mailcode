<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;
use stdClass;

class Mailcode_Parser_Safeguard_DelimiterValidator extends OperationResult
{
    const ERROR_EMPTY_DELIMITER = 73601;
    const ERROR_INVALID_DELIMITER = 73602;
    const ERROR_DELIMITER_TOO_SHORT = 73603;
    const ERROR_NUMBERS_IN_DELIMITER = 73604;

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

        if(!preg_match('/\A[^0-9*].*[^0-9*]\z/x', $this->delimiter))
        {
            return $this->makeError(
                'The delimiter may not begin or end with a number.',
                self::ERROR_NUMBERS_IN_DELIMITER
            );
        }

        if(strstr($this->delimiter, '*') !== false)
        {
            return $this->makeError(
                'The delimiter may not contain the * character.',
                self::ERROR_INVALID_DELIMITER
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
