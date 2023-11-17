<?php

declare(strict_types=1);

namespace Mailcode\Decrypt;

class DecryptSettings
{
    private static ?string $defaultKeyName = null;

    /**
     * @param string|NULL $decryptionKey A decryption key name, or NULL to reset to the default.
     * @return void
     */
    public static function setDefaultKeyName(?string $decryptionKey): void
    {
        self::$defaultKeyName = $decryptionKey;
    }

    /**
     * Gets the default decryption key name for decryption.
     *
     * @return string
     */
    public static function getDefaultKeyName(): ?string
    {
        return self::$defaultKeyName;
    }
}
