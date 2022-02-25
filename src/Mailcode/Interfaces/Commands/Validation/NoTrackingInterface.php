<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\NoTrackingInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\NoTrackingInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Traits\Commands\Validation\NoTrackingTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see NoTrackingTrait
 */
interface NoTrackingInterface
{
    public const VALIDATION_NAME_NO_TRACKING = 'no_tracking';

    public function isTrackingEnabled() : bool;
    public function getNoTrackingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
