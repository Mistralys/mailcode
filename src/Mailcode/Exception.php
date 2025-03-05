<?php
/**
 * @package Mailcode
 * @subpackage Core
 */

namespace Mailcode;

use AppUtils\BaseException;

/**
 * Mailcode exception.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Exception extends BaseException
{
    public const ERROR_CACHE_FOLDER_NOT_SET = 173901;

    public function __construct(string $message, ?string $details = null, $code = null, $previous = null)
    {
        if(defined('TESTS_ROOT') && !empty($details)) {
            $message .= PHP_EOL.
                'Details:'.PHP_EOL.
                $details;
        }

        parent::__construct($message, $details, $code, $previous);
    }

    /**
     * @var Mailcode_Collection|null
     */
    private ?Mailcode_Collection $collection = null;

    public function setCollection(Mailcode_Collection $collection) : void
    {
        $this->collection = $collection;
    }

    /**
     * @return Mailcode_Collection|null
     */
    public function getCollection(): ?Mailcode_Collection
    {
        return $this->collection;
    }

    public function hasCollection() : bool
    {
        return isset($this->collection);
    }
}
