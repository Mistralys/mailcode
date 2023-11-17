<?php

declare(strict_types=1);

namespace Mailcode\Decrypt;

use Mailcode\Interfaces\Commands\Validation\DecryptInterface;

class DecryptSettings
{
    private static ?string $defaultKey = null;

    /**
     * @param string|NULL $decryptionKey A decryption key
     * @return void
     */
    public static function setDefaultKey(?string $decryptionKey): void
    {
        self::$defaultKey = $decryptionKey;
    }

    /**
     * Gets the default decryption key for decryption. If not set via
     * {@see self::setDefaultDecryptionKey()}, this defaults to {@see DecryptInterface::DEFAULT_DECRYPTION_KEY}.
     *
     * @return string
     */
    public static function getDefaultKey(): string
    {
        return self::$defaultKey ?? DecryptInterface::DEFAULT_DECRYPTION_KEY;
    }
}
