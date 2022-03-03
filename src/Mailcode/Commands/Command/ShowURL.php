<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Commands_Command_ShowURL} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Commands_Command_ShowURL
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\TrackableInterface;
use Mailcode\Interfaces\Commands\Validation\QueryParamsInterface;
use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;
use Mailcode\Interfaces\Commands\Validation\NoTrackingInterface;
use Mailcode\Traits\Commands\Validation\QueryParamsTrait;
use Mailcode\Traits\Commands\Validation\TrackingIDTrait;
use Mailcode\Traits\Commands\Validation\NoTrackingTrait;

/**
 * Mailcode command: `showurl` to format and display a URL
 * with or without tracking. The URL is specified as the
 * content of the command, to allow Mailcode syntax to build
 * the URL or retrieve it from variables.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowURL
    extends Mailcode_Commands_Command
    implements
    Mailcode_Interfaces_Commands_ProtectedContent,
    TrackableInterface,
    QueryParamsInterface
{
    use Mailcode_Traits_Commands_ProtectedContent;
    use NoTrackingTrait;
    use TrackingIDTrait;
    use QueryParamsTrait;

    public function getName() : string
    {
        return 'showurl';
    }

    public function getLabel() : string
    {
        return t('Show URL with or without tracking.');
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
            Mailcode_Interfaces_Commands_ProtectedContent::VALIDATION_NAME_CONTENT_ID,
            TrackingIDInterface::VALIDATION_NAME_TRACKING_ID,
            QueryParamsInterface::VALIDATION_NAME_QUERY_PARAMS,
            Mailcode_Interfaces_Commands_ProtectedContent::VALIDATION_NAME_NESTED_MAILCODE,
        );
    }

    public function generatesContent() : bool
    {
        return true;
    }

    public function getURL() : string
    {
        return $this->getContentTrimmed();
    }

    public function isMailcodeEnabled() : bool
    {
        return true;
    }
}
