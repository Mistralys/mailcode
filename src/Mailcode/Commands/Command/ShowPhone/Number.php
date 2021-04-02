<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Commands_Command_ShowPhone_Number
{
    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $localExample;

    /**
     * @var string
     */
    private $internationalExample;

    /**
     * @var string
     */
    private $label;

    public function __construct(string $countryCode, string $label, string $localExample, string $internationalExample)
    {
        $this->countryCode = $countryCode;
        $this->label = $label;
        $this->localExample = $localExample;
        $this->internationalExample = $internationalExample;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function getLocalExample(): string
    {
        return $this->localExample;
    }

    /**
     * @return string
     */
    public function getInternationalExample(): string
    {
        return $this->internationalExample;
    }
}
