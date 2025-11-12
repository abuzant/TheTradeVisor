<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class TranslationHelper
{
    protected static $translations = [];
    protected static $currentLocale = 'en';

    public static function setLocale($locale)
    {
        if (in_array($locale, ['en', 'ar', 'ru'])) {
            self::$currentLocale = $locale;
            session(['locale' => $locale]);
        }
    }

    public static function getLocale()
    {
        return session('locale', self::$currentLocale);
    }

    public static function get($key, $replacements = [])
    {
        $locale = self::getLocale();
        
        if (!isset(self::$translations[$locale])) {
            $path = resource_path("lang/{$locale}/public.json");
            if (File::exists($path)) {
                self::$translations[$locale] = json_decode(File::get($path), true);
            } else {
                self::$translations[$locale] = [];
            }
        }

        $keys = explode('.', $key);
        $value = self::$translations[$locale];

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key;
            }
        }

        // Replace placeholders
        foreach ($replacements as $placeholder => $replacement) {
            $value = str_replace("{{" . $placeholder . "}}", $replacement, $value);
        }

        return $value;
    }
}
