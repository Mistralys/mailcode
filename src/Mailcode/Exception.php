<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Exception} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see \Mailcode\Mailcode_Exception
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
