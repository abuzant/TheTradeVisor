<?php

namespace App\Support;

class ApiKeyValidator
{
    public const STANDARD_PATTERN = '/^tvsr_[A-Za-z0-9]{64}$/';
    public const ENTERPRISE_PATTERN = '/^ent_[A-Za-z0-9]{64}$/';
    public const STANDARD_LENGTH = 69; // tvsr_ + 64 chars
    public const ENTERPRISE_LENGTH = 68; // ent_ + 64 chars

    public static function hasAuthorizationHeader(?string $authorization): bool
    {
        return is_string($authorization) && trim($authorization) !== '';
    }

    public static function extractKeyFromAuthorization(?string $authorization): ?string
    {
        if (!self::hasAuthorizationHeader($authorization)) {
            return null;
        }

        $authorization = trim($authorization);

        if (str_starts_with(strtolower($authorization), 'bearer ')) {
            $authorization = substr($authorization, 7);
        }

        $authorization = trim($authorization);

        return $authorization !== '' ? $authorization : null;
    }

    public static function isStandardKeyFormat(?string $key): bool
    {
        return is_string($key) && preg_match(self::STANDARD_PATTERN, $key) === 1;
    }

    public static function isEnterpriseKeyFormat(?string $key): bool
    {
        return is_string($key) && preg_match(self::ENTERPRISE_PATTERN, $key) === 1;
    }

    public static function detectKeyType(?string $key): ?string
    {
        if (!is_string($key)) {
            return null;
        }

        if (self::isStandardKeyFormat($key) && strlen($key) === self::STANDARD_LENGTH) {
            return 'standard';
        }

        if (self::isEnterpriseKeyFormat($key) && strlen($key) === self::ENTERPRISE_LENGTH) {
            return 'enterprise';
        }

        return null;
    }

    public static function isKnownKeyFormat(?string $key): bool
    {
        return self::detectKeyType($key) !== null;
    }
}
