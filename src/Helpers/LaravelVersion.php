<?php

namespace Webekspres\FonnteOtp\Helpers;

class LaravelVersion
{
    /**
     * Get the Laravel framework version.
     *
     * @return string
     */
    public static function get(): string
    {
        try {
            if (class_exists('\Illuminate\Foundation\Application')) {
                return app()->version();
            }
        } catch (\Exception $e) {
            // Fall through
        }

        return '8.0'; // Default fallback
    }

    /**
     * Check if Laravel version is greater than or equal to a specific version.
     *
     * @param string $version
     * @return bool
     */
    public static function gte(string $version): bool
    {
        return version_compare(static::get(), $version, '>=');
    }

    /**
     * Check if Laravel version is less than a specific version.
     *
     * @param string $version
     * @return bool
     */
    public static function lt(string $version): bool
    {
        return version_compare(static::get(), $version, '<');
    }
}