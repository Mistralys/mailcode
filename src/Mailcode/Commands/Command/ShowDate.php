<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowDate} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowDate
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
use Mailcode\Traits\Commands\Validation\TimezoneTrait;

/**
 * Mailcode command: show a date variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowDate
    extends Mailcode_Commands_ShowBase
    implements
    TimezoneInterface
{
    use TimezoneTrait;

    /**
     * The date format string.
     * @var string
     */
    private string $formatString;

    /**
     * @var Mailcode_Date_FormatInfo
     */
    private Mailcode_Date_FormatInfo $formatInfo;

    public function getName(): string
    {
        return 'showdate';
    }

    public function getLabel(): string
    {
        return t('Show date variable');
    }

    protected function getValidations(): array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            'check_format',
            TimezoneInterface::VALIDATION_TIMEZONE_NAME
        );
    }

    protected function init(): void
    {
        $this->formatInfo = Mailcode_Factory::createDateInfo();
        $this->formatString = $this->formatInfo->getDefaultFormat();

        parent::init();
    }

    protected function validateSyntax_check_format(): void
    {
        $tokens = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        // no format specified? Use the default one.
        if (empty($tokens)) {
            return;
        }

        $format = $tokens[0]->getText();

        $this->parseFormatString($format);
    }

    private function parseFormatString(string $format): void
    {
        $result = $this->formatInfo->validateFormat($format);

        if ($result->isValid()) {
            $this->formatString = $format;
            return;
        }

        $this->validationResult->makeError(
            $result->getErrorMessage(),
            $result->getCode()
        );
    }

    /**
     * Retrieves the format string used to format the date.
     *
     * @return string A PHP compatible date format string.
     */
    public function getFormatString(): string
    {
        return $this->formatString;
    }
}
