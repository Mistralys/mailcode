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
    Mailcode_Interfaces_Commands_Validation_NoTracking,
    Mailcode_Interfaces_Commands_Validation_TrackingID,
    Mailcode_Interfaces_Commands_Validation_QueryParams
{
    use Mailcode_Traits_Commands_ProtectedContent;
    use Mailcode_Traits_Commands_Validation_NoTracking;
    use Mailcode_Traits_Commands_Validation_TrackingID;
    use Mailcode_Traits_Commands_Validation_QueryParams;

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
            Mailcode_Interfaces_Commands_Validation_NoTracking::VALIDATION_NAME_NO_TRACKING,
            Mailcode_Interfaces_Commands_Validation_TrackingID::VALIDATION_NAME_TRACKING_ID,
            Mailcode_Interfaces_Commands_Validation_QueryParams::VALIDATION_NAME_QUERY_PARAMS
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
}
